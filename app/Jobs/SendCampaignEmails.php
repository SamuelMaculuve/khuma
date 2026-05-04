<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Models\Clients;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Support\TenantMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function __construct(public readonly int $campaignId) {}

    public function handle(): void
    {
        $campaign = EmailCampaign::with('company')->findOrFail($this->campaignId);
        $campaign->update(['status' => 'sending']);

        $company = $campaign->company;
        $mailer  = ($company && $company->mail_provision_status === 'ready')
            ? TenantMailer::for($company, 'campaign')
            : Mail::mailer();

        $filters = $campaign->filters ?? [];

        $query = Clients::query()
            ->where('company_id', $campaign->company_id)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if (!empty($filters['lead_status'])) {
            $query->whereHas('leads', function ($q) use ($filters) {
                $q->whereIn('status', (array) $filters['lead_status']);
            });
        }

        $sentCount   = 0;
        $failedCount = 0;

        $query->chunk(100, function ($clients) use ($campaign, $company, $mailer, &$sentCount, &$failedCount) {
            foreach ($clients as $client) {
                $log = EmailCampaignLog::create([
                    'email_campaign_id' => $campaign->id,
                    'client_id'         => $client->id,
                    'email_address'     => $client->email,
                    'status'            => 'pending',
                ]);

                try {
                    $mailable = (new CampaignEmail($campaign, $client, $company?->name ?? ''))
                        ->replyTo(
                            $company?->mail_provision_status === 'ready'
                                ? 'campaign@' . $company->mail_subdomain . '.' . config('services.mail_tenant.parent_domain')
                                : config('mail.from.address')
                        );
                    $mailer->to($client->email)->send($mailable);

                    $log->update(['status' => 'sent', 'sent_at' => now()]);
                    $sentCount++;
                } catch (\Throwable $e) {
                    $log->update([
                        'status'        => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                    $failedCount++;
                    Log::error("Campaign #{$campaign->id} failed for client #{$client->id}", [
                        'email' => $client->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        $campaign->update([
            'status'       => 'sent',
            'sent_count'   => $sentCount,
            'failed_count' => $failedCount,
            'sent_at'      => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        EmailCampaign::find($this->campaignId)?->update(['status' => 'failed']);
        Log::error("SendCampaignEmails job failed for campaign #{$this->campaignId}", [
            'error' => $e->getMessage(),
        ]);
    }
}
