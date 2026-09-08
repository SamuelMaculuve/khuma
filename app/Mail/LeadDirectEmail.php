<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadDirectEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string  $clientName,
        public readonly string  $messageContent,
        public readonly string  $leadReference,
        public readonly string  $agentName,
        public readonly string  $smtpMessageId = '',
        public readonly ?string $inReplyTo = null,
        public readonly ?string $references = null,
        public readonly ?string $attachmentPath = null,
        public readonly ?string $attachmentName = null,
    ) {}

    public function attachments(): array
    {
        if (! $this->attachmentPath) {
            return [];
        }
        return [
            Attachment::fromStorageDisk('public', $this->attachmentPath)
                ->as($this->attachmentName ?? basename($this->attachmentPath)),
        ];
    }

    public function envelope(): Envelope
    {
        $mid    = $this->smtpMessageId;
        $reply  = $this->inReplyTo;
        $refs   = $this->references;

        return new Envelope(
            subject: "Re: Lead #{$this->leadReference}",
            using: [
                function (\Symfony\Component\Mime\Email $message) use ($mid, $reply, $refs) {
                    if ($mid) {
                        $message->getHeaders()->addIdHeader('Message-ID', $mid);
                    }
                    if ($reply) {
                        $message->getHeaders()->addTextHeader('In-Reply-To', "<{$reply}>");
                    }
                    if ($refs) {
                        $message->getHeaders()->addTextHeader('References', $refs);
                    }
                },
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-direct',
            text: 'emails.lead-direct-text',
        );
    }
}
