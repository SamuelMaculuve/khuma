<?php

namespace App\Support;

use App\Models\Companies;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class TenantMailer
{
    public static function for(Companies $company, string $alias = 'campaign'): Mailer
    {
        if ($company->mail_provision_status !== 'ready') {
            throw new RuntimeException("Company #{$company->id} mail is not provisioned (status: {$company->mail_provision_status}).");
        }

        $parent = (string) config('services.mail_tenant.parent_domain');
        $fqdn   = "{$company->mail_subdomain}.{$parent}";
        $from   = "{$alias}@{$fqdn}";

        $username = "{$company->mail_inbox_local_part}@{$fqdn}";
        $password = Crypt::decryptString($company->mail_inbox_password);

        $key = "tenant-{$company->id}-{$alias}";

        config([
            "mail.mailers.{$key}" => [
                'transport'  => 'smtp',
                'scheme'     => 'smtp',
                'host'       => (string) config('services.mailcow.smtp_host'),
                'port'       => (int) config('services.mailcow.smtp_port', 587),
                'encryption' => 'tls',
                'username'   => $username,
                'password'   => $password,
                'timeout'    => 30,
                'local_domain' => $fqdn,
            ],
        ]);

        /** @var MailManager $manager */
        $manager = app(MailManager::class);
        $manager->purge($key);

        $mailer = $manager->mailer($key);
        // Authenticate with the shared inbox, but present the tenant alias to recipients.
        $mailer->alwaysFrom($from, $company->name);
        $mailer->alwaysReplyTo($from, $company->name);

        return $mailer;
    }

    public static function fromAddress(Companies $company, string $alias = 'campaign'): string
    {
        $parent = (string) config('services.mail_tenant.parent_domain');
        return "{$alias}@{$company->mail_subdomain}.{$parent}";
    }
}
