<?php

namespace App\Jobs;

use App\Models\Clients;
use App\Models\Companies;
use App\Models\EmailCampaignLog;
use App\Models\InboundEmail;
use App\Models\Leads;
use App\Models\Messages;
use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FetchTenantInboundMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries   = 1;

    public function __construct(public readonly int $companyId) {}

    public function handle(): void
    {
        if (! function_exists('imap_open')) {
            Log::warning('ext-imap is not installed; skipping inbound fetch.');
            return;
        }

        $company = Companies::find($this->companyId);
        if (! $company || $company->mail_provision_status !== 'ready') {
            return;
        }

        $host = (string) config('services.mailcow.imap_host');
        $port = (int)    config('services.mailcow.imap_port', 993);
        $parent = (string) config('services.mail_tenant.parent_domain');
        $fqdn   = "{$company->mail_subdomain}.{$parent}";
        $user   = "{$company->mail_inbox_local_part}@{$fqdn}";
        $pass   = Crypt::decryptString($company->mail_inbox_password);

        $mailbox = "{{$host}:{$port}/imap/ssl}INBOX";
        $inbox   = @imap_open($mailbox, $user, $pass, 0, 1);

        if (! $inbox) {
            throw new RuntimeException("IMAP login failed for {$user}: " . imap_last_error());
        }

        try {
            $uids = imap_search($inbox, 'UNSEEN', SE_UID) ?: [];

            foreach ($uids as $uid) {
                try {
                    $this->ingest($company, $inbox, (int) $uid, $fqdn);
                    imap_setflag_full($inbox, (string) $uid, '\\Seen', ST_UID);
                } catch (\Throwable $e) {
                    Log::error("Inbound ingest failed for company #{$company->id} uid {$uid}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $company->update(['mail_last_inbound_fetch_at' => now()]);
        } finally {
            imap_close($inbox);
        }
    }

    private function ingest(Companies $company, $inbox, int $uid, string $fqdn): void
    {
        $headerRaw = imap_fetchheader($inbox, $uid, FT_UID);
        $headers   = imap_rfc822_parse_headers($headerRaw);

        $to = $this->addressFromList($headers->to ?? []);
        if (! $to || ! str_ends_with(strtolower($to), '@' . strtolower($fqdn)) && ! $this->deliveredToMatches($headerRaw, $fqdn)) {
            $toFromDelivered = $this->extractDeliveredTo($headerRaw, $fqdn);
            if ($toFromDelivered) {
                $to = $toFromDelivered;
            }
        }

        $alias = $to ? strtolower(strtok($to, '@')) : 'catchall';

        $from     = $this->addressFromList($headers->from ?? []);
        $fromName = isset($headers->from[0]->personal) ? (string) $headers->from[0]->personal : null;
        $subject  = isset($headers->subject) ? $this->decode($headers->subject) : null;
        $msgId    = isset($headers->message_id) ? trim($headers->message_id, '<>') : null;
        $inReply  = isset($headers->in_reply_to) ? trim($headers->in_reply_to, '<>') : null;
        $received = isset($headers->date) ? date('Y-m-d H:i:s', strtotime($headers->date)) : now();

        if ($msgId && InboundEmail::where('message_id', $msgId)->exists()) {
            return;
        }

        [$text, $html] = $this->extractBodies($inbox, $uid);

        $email = InboundEmail::create([
            'company_id'   => $company->id,
            'alias'        => $this->aliasBucket($company, $alias),
            'to_address'   => $to,
            'from_address' => $from,
            'from_name'    => $fromName,
            'subject'      => $subject,
            'message_id'   => $msgId,
            'in_reply_to'  => $inReply,
            'body_text'    => $text,
            'body_html'    => $html,
            'headers'      => $this->headersToArray($headers),
            'received_at'  => $received,
        ]);

        $this->route($company, $email);
    }

    private function route(Companies $company, InboundEmail $email): void
    {
        try {
            if ($email->alias === 'bounce') {
                EmailCampaignLog::whereHas('campaign', fn ($q) => $q->where('company_id', $company->id))
                    ->where('email_address', $email->from_address)
                    ->latest()
                    ->first()
                    ?->update(['status' => 'failed', 'error_message' => 'Bounce: ' . ($email->subject ?? '')]);

                $email->update(['status' => 'processed']);
                return;
            }

            if ($this->routeCampaignReply($company, $email)) {
                $email->update(['status' => 'processed']);
                return;
            }

            // For commercial@, campaign@, catchall@ — link reply to the originating lead
            $lead = $this->resolveLeadFromEmail($company, $email);

            if ($lead) {
                $rawBody = $email->body_text ?? strip_tags($email->body_html ?? '');
                $content = $this->stripQuotedContent($rawBody);

                Messages::create([
                    'message_id' => $email->message_id ?? 'inbound-' . Str::uuid(),
                    'message_to' => $lead->client?->email ?? $email->from_address,
                    'lead_id'    => $lead->id,
                    'sender_id'  => null,
                    'client_id'  => $lead->client_id,
                    'channel'    => 'email',
                    'direction'  => 'inbound',
                    'content'    => $content ?: $rawBody,
                ]);
            }

            $email->update(['status' => 'processed']);
        } catch (\Throwable $e) {
            $email->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    private function routeCampaignReply(Companies $company, InboundEmail $email): bool
    {
        $log = null;

        if ($email->in_reply_to) {
            $log = EmailCampaignLog::with('campaign.replyTeam')
                ->whereHas('campaign', fn ($q) => $q->where('company_id', $company->id))
                ->where('message_id', $email->in_reply_to)
                ->first();
        }

        if (! $log && $email->from_address) {
            $log = EmailCampaignLog::with('campaign.replyTeam')
                ->whereHas('campaign', fn ($q) => $q->where('company_id', $company->id))
                ->where('email_address', $email->from_address)
                ->where('status', 'sent')
                ->latest('sent_at')
                ->first();
        }

        if (! $log || ! $log->campaign) {
            return false;
        }

        $campaign = $log->campaign;
        $replyTeam = $this->teamForInboundEmail($company, $email);
        if ($campaign->reply_team_id && $replyTeam?->id !== $campaign->reply_team_id) {
            return false;
        }

        $lead = null;

        if ($campaign->reply_action === \App\Models\EmailCampaign::REPLY_CREATE_IF_NONE) {
            $client = $this->findOrCreateClientFromEmail($company, $email);
            $lead = Leads::where('company_id', $company->id)
                ->where('client_id', $client->id)
                ->when($campaign->reply_team_id, fn ($query) => $query->where('team_id', $campaign->reply_team_id))
                ->whereNotIn('status', ['won', 'lost'])
                ->latest()
                ->first();

            if (! $lead) {
                $lead = $this->createLeadFromCampaignReply($company, $email, $client, $campaign->name, $campaign->reply_team_id);
            }
        } elseif ($campaign->reply_action === \App\Models\EmailCampaign::REPLY_ALWAYS_CREATE) {
            $client = $this->findOrCreateClientFromEmail($company, $email);
            $lead = $this->createLeadFromCampaignReply($company, $email, $client, $campaign->name, $campaign->reply_team_id);
        }

        $log->forceFill([
            'replied_at' => $log->replied_at ?: now(),
            'reply_count' => ((int) $log->reply_count) + 1,
            'inbound_email_id' => $email->id,
            'lead_id' => $lead?->id ?: $log->lead_id,
        ])->save();

        if ($lead) {
            $rawBody = $email->body_text ?? strip_tags($email->body_html ?? '');
            $content = $this->stripQuotedContent($rawBody);

            Messages::create([
                'message_id' => $email->message_id ?? 'inbound-' . Str::uuid(),
                'message_to' => $lead->client?->email ?? $email->from_address,
                'lead_id'    => $lead->id,
                'sender_id'  => null,
                'client_id'  => $lead->client_id,
                'channel'    => 'email',
                'direction'  => 'inbound',
                'content'    => $content ?: $rawBody,
            ]);
        }

        return true;
    }

    private function findOrCreateClientFromEmail(Companies $company, InboundEmail $email): Clients
    {
        $client = Clients::where('company_id', $company->id)
            ->where('email', $email->from_address)
            ->first();

        if ($client) {
            return $client;
        }

        return Clients::create([
            'company_id' => $company->id,
            'name'       => $email->from_name ?: explode('@', $email->from_address)[0],
            'email'      => $email->from_address,
        ]);
    }

    private function createLeadFromCampaignReply(Companies $company, InboundEmail $email, Clients $client, string $campaignName, ?int $teamId = null): Leads
    {
        return Leads::create([
            'client_id'  => $client->id,
            'company_id' => $company->id,
            'team_id'    => $teamId,
            'reference'  => 'LD-' . $company->id . '-' . strtoupper(Str::random(8)),
            'title'      => 'Resposta à campanha: ' . $campaignName,
            'description'=> $email->subject,
            'status'     => 'new',
            'source'     => 'email_campaign',
        ]);
    }

    private function resolveLeadFromEmail(Companies $company, InboundEmail $email): ?Leads
    {
        $team = $this->teamForInboundEmail($company, $email);

        // 1. Prefer exact thread matching against the outbound email Message-ID.
        if ($email->in_reply_to) {
            $message = Messages::where('message_id', $email->in_reply_to)
                ->whereHas('lead', fn ($query) => $query->where('company_id', $company->id))
                ->first();

            if ($message?->lead) {
                return $message->lead;
            }
        }

        // 2. Try to find lead by reference in subject (e.g. "Re: Lead #LD-3-XXXX")
        if ($email->subject && preg_match('/#(LD-\d+-[A-Z0-9]+)/', $email->subject, $matches)) {
            $lead = Leads::where('company_id', $company->id)
                ->where('reference', $matches[1])
                ->first();
            if ($lead) {
                return $lead;
            }
        }

        // 3. Find or create client by from_address
        $client = Clients::where('company_id', $company->id)
            ->where('email', $email->from_address)
            ->first();

        if (! $client) {
            $clientName = $email->from_name ?: explode('@', $email->from_address)[0];
            $client = Clients::create([
                'company_id' => $company->id,
                'name'       => $clientName,
                'email'      => $email->from_address,
            ]);
        }

        // 4. Find open lead or create a new one
        $lead = Leads::where('company_id', $company->id)
            ->where('client_id', $client->id)
            ->when($team, fn ($query) => $query->where('team_id', $team->id))
            ->whereNotIn('status', ['won', 'lost'])
            ->latest()
            ->first();

        if (! $lead) {
            $lead = Leads::create([
                'client_id'  => $client->id,
                'company_id' => $company->id,
                'team_id'    => $team?->id,
                'reference'  => 'LD-' . $company->id . '-' . strtoupper(Str::random(8)),
                'title'      => $email->subject ?: 'Email para ' . ($team?->name ?? $email->to_address ?? $email->from_address),
                'status'     => 'new',
                'source'     => $team ? 'email_team:' . $team->email_alias : 'email',
            ]);
        }

        return $lead;
    }

    private function stripQuotedContent(string $body): string
    {
        $lines  = explode("\n", $body);
        $result = [];

        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            // Stop at quoted lines (>) or "On DATE ... wrote:" patterns
            if (str_starts_with($trimmed, '>')) {
                break;
            }
            if (preg_match('/^(On .+wrote:|.+escreveu.+:)\s*$/i', rtrim($line))) {
                break;
            }
            if (preg_match('/^(De|From|Para|To|Data|Date|Assunto|Subject):\s*/i', rtrim($line))) {
                break;
            }
            $result[] = $line;
        }

        return trim(implode("\n", $result));
    }

    private function aliasBucket(Companies $company, string $alias): string
    {
        if ($this->teamForAlias($company, $alias)) {
            return $alias;
        }

        $allowed = (array) config('services.mail_tenant.aliases');
        return in_array($alias, $allowed, true) ? $alias : 'catchall';
    }

    private function teamForAlias(Companies $company, ?string $alias): ?Team
    {
        if (! $alias) {
            return null;
        }

        return Team::where('company_id', $company->id)
            ->where('email_alias', $alias)
            ->first();
    }

    private function teamForInboundEmail(Companies $company, InboundEmail $email): ?Team
    {
        $team = $this->teamForAlias($company, $email->alias);

        if ($team || ! $email->to_address) {
            return $team;
        }

        return $this->teamForAlias($company, strtolower(strtok($email->to_address, '@')));
    }

    private function addressFromList(array $list): ?string
    {
        if (empty($list)) {
            return null;
        }
        $first = $list[0];
        return isset($first->mailbox, $first->host) ? "{$first->mailbox}@{$first->host}" : null;
    }

    private function extractBodies($inbox, int $uid): array
    {
        $structure = imap_fetchstructure($inbox, $uid, FT_UID);
        $text = null;
        $html = null;

        if (! isset($structure->parts) || empty($structure->parts)) {
            $body = imap_body($inbox, $uid, FT_UID);
            $decoded = $this->decodeBody($body, $structure->encoding ?? 0);
            if (($structure->subtype ?? 'PLAIN') === 'HTML') {
                $html = $decoded;
            } else {
                $text = $decoded;
            }
            return [$text, $html];
        }

        $this->walkParts($inbox, $uid, $structure->parts, '', $text, $html);
        return [$text, $html];
    }

    private function walkParts($inbox, int $uid, array $parts, string $prefix, ?string &$text, ?string &$html): void
    {
        foreach ($parts as $i => $part) {
            $section = $prefix === '' ? (string) ($i + 1) : "{$prefix}.".($i + 1);
            if (! empty($part->parts)) {
                $this->walkParts($inbox, $uid, $part->parts, $section, $text, $html);
                continue;
            }

            $data = imap_fetchbody($inbox, $uid, $section, FT_UID);
            $decoded = $this->decodeBody($data, $part->encoding ?? 0);
            $subtype = strtoupper($part->subtype ?? '');

            if ($subtype === 'PLAIN' && $text === null) {
                $text = $decoded;
            } elseif ($subtype === 'HTML' && $html === null) {
                $html = $decoded;
            }
        }
    }

    private function decodeBody(string $data, int $encoding): string
    {
        return match ($encoding) {
            3       => base64_decode($data, true) ?: $data,
            4       => quoted_printable_decode($data),
            default => $data,
        };
    }

    private function decode(string $value): string
    {
        $parts = imap_mime_header_decode($value);
        return collect($parts)->map(fn ($p) => $p->text)->implode('');
    }

    private function headersToArray(object $headers): array
    {
        return json_decode(json_encode($headers), true) ?? [];
    }

    private function deliveredToMatches(string $headerRaw, string $fqdn): bool
    {
        return (bool) preg_match('/^Delivered-To: .+@' . preg_quote($fqdn, '/') . '/mi', $headerRaw);
    }

    private function extractDeliveredTo(string $headerRaw, string $fqdn): ?string
    {
        if (preg_match('/^Delivered-To: (.+@' . preg_quote($fqdn, '/') . ')/mi', $headerRaw, $m)) {
            return trim($m[1]);
        }
        return null;
    }
}
