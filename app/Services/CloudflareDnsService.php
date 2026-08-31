<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudflareDnsService
{
    private const BASE = 'https://api.cloudflare.com/client/v4';

    public function __construct(
        private readonly string $apiToken,
        private readonly string $zoneId,
    ) {}

    public static function fromConfig(): self
    {
        $token = (string) config('services.cloudflare.api_token');
        $zone  = (string) config('services.cloudflare.zone_id');

        if ($token === '' || $zone === '') {
            throw new RuntimeException('Cloudflare is not configured (CLOUDFLARE_API_TOKEN / CLOUDFLARE_ZONE_ID).');
        }

        return new self($token, $zone);
    }

    public function createMx(string $name, string $mailHost, int $priority = 10, int $ttl = 1): array
    {
        return $this->createRecord([
            'type'     => 'MX',
            'name'     => $name,
            'content'  => $mailHost,
            'priority' => $priority,
            'ttl'      => $ttl,
            'proxied'  => false,
        ]);
    }

    public function createTxt(string $name, string $value, int $ttl = 1): array
    {
        return $this->createRecord([
            'type'    => 'TXT',
            'name'    => $name,
            'content' => $value,
            'ttl'     => $ttl,
            'proxied' => false,
        ]);
    }

    public function deleteRecord(string $recordId): array
    {
        $response = $this->client()->delete("/zones/{$this->zoneId}/dns_records/{$recordId}");

        if (! $response->successful()) {
            throw new RuntimeException("Cloudflare delete failed: HTTP {$response->status()} - {$response->body()}");
        }

        return $response->json() ?? [];
    }

    private function createRecord(array $payload): array
    {
        $response = $this->client()->post("/zones/{$this->zoneId}/dns_records", $payload);

        if ($response->status() === 400) {
            $errors = $response->json('errors') ?? [];
            $alreadyExists = collect($errors)->contains('code', 81058);
            if ($alreadyExists) {
                return $this->findExistingRecord($payload['type'], $payload['name']) ?? ['id' => null];
            }
        }

        if (! $response->successful() || $response->json('success') !== true) {
            throw new RuntimeException("Cloudflare create {$payload['type']} {$payload['name']} failed: HTTP {$response->status()} - {$response->body()}");
        }

        return $response->json('result') ?? [];
    }

    private function findExistingRecord(string $type, string $name): ?array
    {
        $response = $this->client()->get("/zones/{$this->zoneId}/dns_records", [
            'type' => $type,
            'name' => $name,
        ]);

        $results = $response->json('result') ?? [];
        return ! empty($results) ? $results[0] : null;
    }

    private function client(): PendingRequest
    {
        return Http::withToken($this->apiToken)
            ->acceptJson()
            ->baseUrl(self::BASE)
            ->timeout(30);
    }
}
