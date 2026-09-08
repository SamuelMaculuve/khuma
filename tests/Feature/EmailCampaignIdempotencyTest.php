<?php

namespace Tests\Feature;

use App\Jobs\DispatchScheduledEmailCampaigns;
use App\Jobs\SendCampaignEmails;
use App\Models\Clients;
use App\Models\Companies;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class EmailCampaignIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        Mail::clearResolvedInstances();
        Log::clearResolvedInstances();

        parent::tearDown();
    }

    public function test_repeated_job_sends_each_campaign_client_pair_only_once(): void
    {
        Mail::fake();

        [$campaign, $client] = $this->campaignWithClients();

        (new SendCampaignEmails($campaign->id))->handle();
        (new SendCampaignEmails($campaign->id))->handle();

        Mail::assertSent(\App\Mail\CampaignEmail::class, 1);
        $this->assertSame(1, EmailCampaignLog::where('email_campaign_id', $campaign->id)->count());
        $this->assertDatabaseHas('email_campaign_logs', [
            'email_campaign_id' => $campaign->id,
            'client_id' => $client->id,
            'status' => 'sent',
        ]);
    }

    public function test_second_worker_is_blocked_by_campaign_lock(): void
    {
        Mail::fake();

        [$campaign] = $this->campaignWithClients();

        $lock = Cache::lock('email-campaign-send:' . $campaign->id, 3600);
        $this->assertTrue($lock->get());

        try {
            (new SendCampaignEmails($campaign->id))->handle();
        } finally {
            $lock->release();
        }

        Mail::assertNothingSent();
        $this->assertDatabaseMissing('email_campaign_logs', [
            'email_campaign_id' => $campaign->id,
        ]);
    }

    public function test_failed_recipient_remains_recoverable_without_affecting_other_recipients(): void
    {
        [$campaign, $failedClient] = $this->campaignWithClients(2);

        $failedLog = EmailCampaignLog::create([
            'email_campaign_id' => $campaign->id,
            'client_id' => $failedClient->id,
            'email_address' => $failedClient->email,
            'tracking_token' => 'failed-token',
            'message_id' => 'campaign-' . $campaign->id . '-client-' . $failedClient->id . '@khuma.local',
            'status' => 'failed',
            'error_message' => 'temporary transport failure',
        ]);

        Mail::fake();

        (new SendCampaignEmails($campaign->id))->handle();

        Mail::assertSent(\App\Mail\CampaignEmail::class, 2);
        $this->assertSame('sent', $failedLog->fresh()->status);
        $this->assertSame(2, EmailCampaignLog::where('email_campaign_id', $campaign->id)->where('status', 'sent')->count());
    }

    public function test_pending_claim_is_not_retried_after_an_ambiguous_interruption(): void
    {
        Mail::fake();

        [$campaign, $client] = $this->campaignWithClients();

        EmailCampaignLog::create([
            'email_campaign_id' => $campaign->id,
            'client_id' => $client->id,
            'email_address' => $client->email,
            'tracking_token' => 'pending-token',
            'message_id' => 'campaign-' . $campaign->id . '-client-' . $client->id . '@khuma.local',
            'status' => 'pending',
        ]);

        (new SendCampaignEmails($campaign->id))->handle();
        (new SendCampaignEmails($campaign->id))->handle();

        Mail::assertNothingSent();
        $this->assertDatabaseHas('email_campaign_logs', [
            'email_campaign_id' => $campaign->id,
            'client_id' => $client->id,
            'status' => 'pending',
        ]);
    }

    public function test_dispatcher_run_more_than_once_does_not_create_a_second_delivery(): void
    {
        Mail::fake();

        [$campaign] = $this->campaignWithClients();
        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => now()->subMinute(),
        ]);

        (new DispatchScheduledEmailCampaigns())->handle();
        (new DispatchScheduledEmailCampaigns())->handle();

        Mail::assertSent(\App\Mail\CampaignEmail::class, 1);
        $this->assertSame('sent', $campaign->fresh()->status);
    }

    public function test_transport_failure_is_logged_as_ambiguous_without_recipient_email(): void
    {
        [$campaign, $client] = $this->campaignWithClients();

        $mailer = Mockery::mock(Mailer::class);
        $mailer->shouldReceive('to')->once()->with($client->email)->andReturnSelf();
        $mailer->shouldReceive('send')->once()->andThrow(new \RuntimeException('transport failed'));

        Mail::shouldReceive('mailer')->once()->andReturn($mailer);
        Log::shouldReceive('error')->once()->withArgs(function (string $message, array $context): bool {
            return $message === 'Campaign email transport outcome is ambiguous.'
                && $context['status'] === 'pending'
                && isset($context['campaign_id'], $context['client_id'])
                && !isset($context['email']);
        });

        (new SendCampaignEmails($campaign->id))->handle();

        $this->assertDatabaseHas('email_campaign_logs', [
            'email_campaign_id' => $campaign->id,
            'client_id' => $client->id,
            'status' => 'pending',
        ]);
    }

    public function test_transport_exception_after_provider_acceptance_does_not_cause_a_second_send(): void
    {
        [$campaign, $client] = $this->campaignWithClients();

        $sendCalls = 0;
        
        // Mock do mailer que falha na primeira chamada
        $mailer = Mockery::mock(Mailer::class);
        $mailer->shouldReceive('to')->with($client->email)->andReturnSelf();
        $mailer->shouldReceive('send')->andReturnUsing(function () use (&$sendCalls) {
            $sendCalls++;
            if ($sendCalls === 1) {
                throw new \RuntimeException('connection dropped after acceptance');
            }
            // Se for chamado uma segunda vez, o teste falha
            $this->fail('Send não deveria ser chamado uma segunda vez');
        });

        // Permite que o mailer seja chamado quantas vezes for necessário
        Mail::shouldReceive('mailer')
            ->andReturn($mailer);

        // Executa o job duas vezes
        (new SendCampaignEmails($campaign->id))->handle();
        (new SendCampaignEmails($campaign->id))->handle();

        // Verifica que o send foi chamado apenas uma vez
        $this->assertSame(1, $sendCalls, 'Send deve ser chamado apenas uma vez');
        
        // Verifica que o log permanece pendente
        $this->assertDatabaseHas('email_campaign_logs', [
            'email_campaign_id' => $campaign->id,
            'client_id' => $client->id,
            'status' => 'pending',
        ]);
    }

    private function campaignWithClients(int $count = 1): array
    {
        $company = Companies::create(['name' => 'Test Company']);
        $user = User::create([
            'name' => 'Campaign Owner',
            'email' => 'owner-' . uniqid() . '@example.test',
            'password' => 'password',
            'company_id' => $company->id,
        ]);

        $campaign = EmailCampaign::create([
            'company_id' => $company->id,
            'created_by' => $user->id,
            'name' => 'Idempotency Test',
            'subject' => 'Test campaign',
            'body_html' => '<p>Hello {{name}}</p>',
            'body_text' => 'Hello {{name}}',
            'status' => 'draft',
        ]);

        $clients = [];
        for ($i = 1; $i <= $count; $i++) {
            $clients[] = Clients::create([
                'company_id' => $company->id,
                'name' => 'Client ' . $i,
                'email' => 'client-' . uniqid() . '-' . $i . '@example.test',
            ]);
        }

        return [$campaign, ...$clients];
    }
}