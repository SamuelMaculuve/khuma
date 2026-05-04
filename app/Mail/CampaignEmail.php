<?php

namespace App\Mail;

use App\Models\Clients;
use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $renderedSubject;
    public string $renderedBody;

    public function __construct(
        public readonly EmailCampaign $campaign,
        public readonly Clients $client,
        public readonly string $companyName,
    ) {
        $vars = [
            '{{name}}'         => $client->name ?? '',
            '{{email}}'        => $client->email ?? '',
            '{{company_name}}' => $companyName,
        ];

        foreach ($campaign->images ?? [] as $key => $url) {
            $vars['{{' . $key . '}}'] = $url ?? '';
        }

        $this->renderedSubject = strtr($campaign->subject, $vars);
        $this->renderedBody    = strtr($campaign->body_html, $vars);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->renderedSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            text: 'emails.campaign-text',
            with: [
                'campaign'   => $this->campaign,
                'clientName' => $this->client->name ?? '',
                'bodyHtml'   => $this->renderedBody,
            ],
        );
    }
}
