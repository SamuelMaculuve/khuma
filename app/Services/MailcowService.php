<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MailcowService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
        private readonly bool $verify = true,
    ) {}

    public static function fromConfig(): self
    {
        $url = (string) config('services.mailcow.url');
        $key = (string) config('services.mailcow.api_key');

        if ($url === '' || $key === '') {
            throw new RuntimeException('Mailcow API is not configured (MAILCOW_API_URL / MAILCOW_API_KEY).');
        }

        return new self(rtrim($url, '/'), $key, (bool) config('services.mailcow.verify', true));
    }

    public function addDomain(string $domain, int $aliases = 50, int $mailboxes = 10, int $defaultQuotaMb = 1024, int $maxQuotaMb = 10240): array
    {
        return $this->call('POST', '/api/v1/add/domain', [
            'domain'      => $domain,
            'description' => $domain,
            'aliases'     => (string) $aliases,
            'mailboxes'   => (string) $mailboxes,
            'defquota'    => (string) $defaultQuotaMb,
            'maxquota'    => (string) $maxQuotaMb,
            'quota'       => (string) ($maxQuotaMb),
            'active'      => '1',
            'rl_value'    => '',
            'rl_frame'    => 's',
            'backupmx'    => '0',
            'relay_all_recipients' => '0',
        ]);
    }

    public function addMailbox(string $localPart, string $domain, string $password, string $name = 'Inbox', int $quotaMb = 1024): array
    {
        return $this->call('POST', '/api/v1/add/mailbox', [
            'local_part' => $localPart,
            'domain'     => $domain,
            'name'       => $name,
            'quota'      => (string) $quotaMb,
            'password'   => $password,
            'password2'  => $password,
            'active'     => '1',
        ]);
    }

    public function addAlias(string $address, string $goto): array
    {
        return $this->call('POST', '/api/v1/add/alias', [
            'address' => $address,
            'goto'    => $goto,
            'active'  => '1',
        ]);
    }

    public function getDkim(string $domain): array
    {
        return $this->call('GET', "/api/v1/get/dkim/{$domain}");
    }

    public function addDkim(string $domain, int $keySize = 2048, string $selector = 'dkim'): array
    {
        return $this->call('POST', '/api/v1/add/dkim', [
            'domains'   => $domain,
            'dkim_selector' => $selector,
            'key_size'  => $keySize,
        ]);
    }

    public function setSenderAcl(string $address, array $allowedSenders): array
    {
        return $this->call('POST', '/api/v1/edit/mailbox', [
            [
                'items' => [$address],
                'attr'  => ['sender_acl' => $allowedSenders],
            ],
        ]);
    }

    public function deleteMailbox(string $address): array
    {
        return $this->call('POST', '/api/v1/delete/mailbox', [$address]);
    }

    public function deleteDomain(string $domain): array
    {
        return $this->call('POST', '/api/v1/delete/domain', [$domain]);
    }

    private function client(): PendingRequest
    {
        return Http::withHeaders([
                'X-API-Key'   => $this->apiKey,
                'Content-Type'=> 'application/json',
                'Accept'      => 'application/json',
            ])
            ->withOptions(['verify' => $this->verify])
            ->baseUrl($this->baseUrl)
            ->timeout(30);
    }

    private function call(string $method, string $path, array $payload = []): array
    {
        $request = $this->client();
        $response = $method === 'GET'
            ? $request->get($path)
            : $request->send($method, $path, ['json' => $payload]);

        if (! $response->successful()) {
            throw new RuntimeException("Mailcow {$method} {$path} failed: HTTP {$response->status()} - {$response->body()}");
        }

        $body = $response->json();

        if (is_array($body) && isset($body[0]['type']) && $body[0]['type'] === 'danger') {
            $msg = (string) ($body[0]['msg'][0] ?? '');
            $alreadyExists = str_contains($msg, '_exists') || str_contains($msg, 'is_alias_or_mailbox');
            if ($alreadyExists) {
                return $body;
            }
            throw new RuntimeException("Mailcow {$method} {$path} rejected: " . json_encode($body));
        }

        return is_array($body) ? $body : ['raw' => $response->body()];
    }
}
