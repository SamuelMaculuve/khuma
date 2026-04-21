<?php

namespace App\Jobs;

use App\Mail\LeadDirectEmail;
use App\Models\Companies;
use App\Support\TenantMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLeadEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly int    $messageId,
        public readonly string $toEmail,
        public readonly string $clientName,
        public readonly string $messageContent,
        public readonly string $leadReference,
        public readonly string $agentName,
        public ?int    $companyId = null,
        public ?int    $leadId = null,
        public string  $smtpMessageId = '',
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null,
    ) {}

    public function handle(): void
    {
        // Find the first outbound email on this lead to use as thread root
        $firstOutbound = \App\Models\Messages::where('lead_id', $this->leadId ?? 0)
            ->where('channel', 'email')
            ->where('direction', 'outbound')
            ->orderBy('id')
            ->value('message_id');

        $smtpMid  = $this->smtpMessageId ?: $this->messageId . '@khuma.mail';
        $inReplyTo = $firstOutbound && $firstOutbound !== $smtpMid ? $firstOutbound : null;
        $references = $inReplyTo ? "<{$inReplyTo}>" : null;

        $mailable = new LeadDirectEmail(
            clientName:      $this->clientName,
            messageContent:  $this->messageContent,
            leadReference:   $this->leadReference,
            agentName:       $this->agentName,
            smtpMessageId:   $smtpMid,
            inReplyTo:       $inReplyTo,
            references:      $references,
            attachmentPath:  $this->attachmentPath,
            attachmentName:  $this->attachmentName,
        );

        $company = $this->companyId ? Companies::find($this->companyId) : null;

        $mailer = ($company && $company->mail_provision_status === 'ready')
            ? TenantMailer::for($company, 'commercial')
            : Mail::mailer();

        $mailer->to($this->toEmail)->send($mailable);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendLeadEmail job failed for message #{$this->messageId}", [
            'to'    => $this->toEmail,
            'error' => $e->getMessage(),
        ]);
    }
}
