<?php

namespace App\Mail;

use App\Models\Clients;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $renderedSubject;
    public string $renderedBody;

    public function __construct(
        public readonly EmailCampaign $campaign,
        public readonly Clients $client,
        public readonly string $companyName,
        public readonly ?EmailCampaignLog $log = null,
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
        $this->renderedBody    = $this->withTracking(strtr($campaign->body_html, $vars));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->campaign->sender_email
                ? new Address($this->campaign->sender_email, $this->campaign->sender_name ?: $this->companyName)
                : null,
            subject: $this->renderedSubject,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            messageId: $this->log?->message_id,
            text: [
                'X-Khuma-Campaign-ID' => (string) $this->campaign->id,
                'X-Khuma-Client-ID' => (string) $this->client->id,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            text: 'emails.campaign-text',
            with: [
                'campaign'    => $this->campaign,
                'clientName'  => $this->client->name ?? '',
                'companyName' => $this->companyName,
                'bodyHtml'    => $this->renderedBody,
            ],
        );
    }

    private function withTracking(string $html): string
    {
        if (! $this->log?->tracking_token) {
            return $html;
        }

        $html = $this->rewriteLinks($html, $this->log->tracking_token);
        $pixel = '<img src="' . e(route('email-campaigns.track.open', $this->log->tracking_token)) . '" width="1" height="1" alt="" style="display:none;width:1px;height:1px;opacity:0;border:0;" />';

        return $html . $pixel;
    }

    private function rewriteLinks(string $html, string $token): string
    {
        return preg_replace_callback('/href=(["\'])(.*?)\1/i', function (array $matches) use ($token) {
            $url = html_entity_decode($matches[2], ENT_QUOTES);

            if ($url === '' || str_starts_with($url, '#') || preg_match('/^(mailto|tel|sms):/i', $url)) {
                return $matches[0];
            }

            $trackedUrl = URL::signedRoute('email-campaigns.track.click', [
                'token' => $token,
                'url' => $url,
            ]);

            return 'href=' . $matches[1] . e($trackedUrl) . $matches[1];
        }, $html) ?? $html;
    }
}
