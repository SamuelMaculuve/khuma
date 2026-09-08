<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Models\Clients;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Support\TenantMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendCampaignEmails implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;
    public int $uniqueFor = 3700;

    public function __construct(public readonly int $campaignId) {}

    public function uniqueId(): string
    {
        return (string) $this->campaignId;
    }

    public function handle(): void
    {
        // A campaign is processed by one worker at a time. This protects the
        // existing campaign log from concurrent firstOrCreate/send races without
        // requiring a schema change.
        $lock = Cache::lock($this->lockKey(), $this->uniqueFor);

        if (! $lock->get()) {
            Log::info('Campaign send skipped because another worker owns the lock.', [
                'campaign_id' => $this->campaignId,
            ]);

            return;
        }

        try {
            $this->sendCampaign();
        } finally {
            optional($lock)->release();
        }
    }

    private function sendCampaign(): void
    {
        $campaign = EmailCampaign::with('company')->findOrFail($this->campaignId);

        if ($campaign->status === 'scheduled' && $campaign->scheduled_at && $campaign->scheduled_at->isFuture()) {
            return;
        }

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

        $query->chunkById(100, function ($clients) use ($campaign, $company, $mailer) {
            foreach ($clients as $client) {
                $log = EmailCampaignLog::firstOrCreate(
                    [
                        'email_campaign_id' => $campaign->id,
                        'client_id'         => $client->id,
                    ],
                    [
                        'email_address'  => $client->email,
                        'tracking_token' => (string) Str::uuid(),
                        'message_id'     => $this->messageId($campaign, $client),
                        'status'         => 'pending',
                    ],
                );

                // A newly-created pending row is the claim for the current
                // attempt and must be sent now. Only an existing pending row
                // represents a previous ambiguous attempt and must be skipped.
                $isNewClaim = $log->wasRecentlyCreated;

                // A pending row is a durable claim for this campaign/client pair.
                // It is deliberately not retried automatically: after an
                // ambiguous transport failure we cannot know whether the SMTP
                // server accepted the message. Retrying would violate at-most-once.
                // Explicit re-send can reset the log under an audited flow later.
                if ($log->status === 'sent' || ($log->status === 'pending' && ! $isNewClaim)) {
                    continue;
                }

                if (! $log->tracking_token || ! $log->message_id) {
                    $log->forceFill([
                        'email_address'  => $client->email,
                        'tracking_token' => $log->tracking_token ?: (string) Str::uuid(),
                        'message_id'     => $log->message_id ?: $this->messageId($campaign, $client),
                    ])->save();
                }

                try {
                    $mailable = (new CampaignEmail($campaign, $client, $company?->name ?? '', $log))
                        ->replyTo(
                            $campaign->reply_to_email
                                ?: ($company?->mail_provision_status === 'ready'
                                    ? 'campaign@' . $company->mail_subdomain . '.' . config('services.mail_tenant.parent_domain')
                                    : config('mail.from.address'))
                        );
                } catch (\Throwable $e) {
                    // Rendering/building failed before the transport was called,
                    // so this attempt is known not to have been delivered.
                    $log->update([
                        'status'        => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);

                    Log::error('Campaign email preparation failed.', [
                        'campaign_id' => $campaign->id,
                        'client_id'   => $client->id,
                        'status'      => 'failed',
                        'exception'   => $e::class,
                    ]);

                    continue;
                }

                try {
                    $mailer->to($client->email)->send($mailable);

                    $log->update(['status' => 'sent', 'sent_at' => now()]);
                    Log::info('Campaign email sent.', [
                        'campaign_id' => $campaign->id,
                        'client_id'   => $client->id,
                        'status'      => 'sent',
                    ]);
                } catch (\Throwable $e) {
                    // Transport failure is deliberately left as `pending`.
                    // The provider may have accepted the message before the
                    // exception reached the worker. Retrying it automatically
                    // would therefore risk a duplicate. An explicit audited
                    // resend flow may reset this claim later.
                    Log::error('Campaign email transport outcome is ambiguous.', [
                        'campaign_id' => $campaign->id,
                        'client_id'   => $client->id,
                        'status'      => 'pending',
                        'exception'   => $e::class,
                    ]);
                }
            }
        });

        $campaign->update([
            'status'       => 'sent',
            'sent_count'   => $campaign->logs()->where('status', 'sent')->count(),
            'failed_count' => $campaign->logs()->where('status', 'failed')->count(),
            'sent_at'      => now(),
        ]);
    }

    private function lockKey(): string
    {
        return 'email-campaign-send:' . $this->campaignId;
    }

    private function messageId(EmailCampaign $campaign, Clients $client): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'khuma.local';

        return sprintf('campaign-%d-client-%d@%s', $campaign->id, $client->id, $host);
    }

    public function failed(\Throwable $e): void
    {
        EmailCampaign::find($this->campaignId)?->update(['status' => 'failed']);

        Log::error('SendCampaignEmails job failed.', [
            'campaign_id' => $this->campaignId,
            'status'      => 'failed',
            'exception'   => $e::class,
        ]);
    }
}
