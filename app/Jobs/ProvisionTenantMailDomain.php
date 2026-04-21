<?php

namespace App\Jobs;

use App\Models\Companies;
use App\Services\CloudflareDnsService;
use App\Services\MailcowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProvisionTenantMailDomain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(public readonly int $companyId) {}

    public function handle(): void
    {
        $company = Companies::findOrFail($this->companyId);

        if ($company->mail_provision_status === 'ready') {
            return;
        }

        $company->update(['mail_provision_status' => 'provisioning', 'mail_provision_error' => null]);

        try {
            $mailcow = MailcowService::fromConfig();
            $dns     = CloudflareDnsService::fromConfig();
        } catch (\Throwable $e) {
            $company->update(['mail_provision_status' => 'failed', 'mail_provision_error' => $e->getMessage()]);
            throw $e;
        }

        $this->doHandle($company, $mailcow, $dns);
    }

    private function doHandle(Companies $company, MailcowService $mailcow, CloudflareDnsService $dns): void
    {

        $parent   = (string) config('services.mail_tenant.parent_domain');
        $mailHost = (string) config('services.mail_tenant.mail_host');
        $aliases  = (array)  config('services.mail_tenant.aliases');
        $inboxLp  = (string) config('services.mail_tenant.inbox_local');
        $spf      = (string) config('services.mail_tenant.spf');
        $dmarc    = (string) config('services.mail_tenant.dmarc_policy');

        $slug      = $this->uniqueSlug($company, $parent);
        $fqdn      = "{$slug}.{$parent}";
        $inboxAddr = "{$inboxLp}@{$fqdn}";
        $password  = Str::password(24, symbols: false);

        try {
            $mailcow->addDomain($fqdn);
            $addResult = $mailcow->addMailbox($inboxLp, $fqdn, $password, "{$company->name} Inbox");
            $alreadyExisted = isset($addResult[0]['type']) && $addResult[0]['type'] === 'danger';
            if ($alreadyExisted) {
                $mailcow->deleteMailbox($inboxAddr);
                $mailcow->addMailbox($inboxLp, $fqdn, $password, "{$company->name} Inbox");
            }

            $aliasAddresses = [];
            foreach ($aliases as $alias) {
                $address = $alias === 'catchall' ? "@{$fqdn}" : "{$alias}@{$fqdn}";
                $mailcow->addAlias($address, $inboxAddr);
                if ($alias !== 'catchall') {
                    $aliasAddresses[] = $address;
                }
            }

            // Allow inbox mailbox to send as any alias on this domain
            $mailcow->setSenderAcl($inboxAddr, array_merge([$inboxAddr], $aliasAddresses));

            try {
                $mailcow->addDkim($fqdn);
            } catch (\Throwable $e) {
                Log::info("DKIM may already exist for {$fqdn}: " . $e->getMessage());
            }

            $dkim = $mailcow->getDkim($fqdn);
            $dkimTxt      = $dkim['dkim_txt']      ?? null;
            $dkimSelector = $dkim['dkim_selector'] ?? 'dkim';

            $created = [];
            $created['mx']    = $dns->createMx($slug, $mailHost)['id']        ?? null;
            $created['spf']   = $dns->createTxt($slug, $spf)['id']            ?? null;
            $created['dmarc'] = $dns->createTxt("_dmarc.{$slug}", $dmarc)['id'] ?? null;

            if ($dkimTxt) {
                $created['dkim'] = $dns->createTxt("{$dkimSelector}._domainkey.{$slug}", $dkimTxt)['id'] ?? null;
            }

            $company->update([
                'mail_subdomain'         => $slug,
                'mail_inbox_local_part'  => $inboxLp,
                'mail_inbox_password'    => Crypt::encryptString($password),
                'mail_dkim_public_key'   => $dkimTxt,
                'mail_dkim_selector'     => $dkimSelector,
                'mail_cloudflare_records'=> $created,
                'mail_provision_status'  => 'ready',
                'mail_provision_error'   => null,
                'mail_provisioned_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            $company->update([
                'mail_provision_status' => 'failed',
                'mail_provision_error'  => $e->getMessage(),
            ]);

            Log::error("Tenant mail provisioning failed for company #{$company->id}", [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function uniqueSlug(Companies $company, string $parent): string
    {
        if ($company->mail_subdomain) {
            return $company->mail_subdomain;
        }

        $base = Str::slug($company->name, '-');
        $base = $base !== '' ? $base : 'tenant-' . $company->id;
        $base = Str::limit($base, 40, '');

        $slug    = $base;
        $attempt = 0;
        while (Companies::where('mail_subdomain', $slug)->where('id', '!=', $company->id)->exists()) {
            $attempt++;
            $slug = "{$base}-{$attempt}";
        }

        return $slug;
    }
}
