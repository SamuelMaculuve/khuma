<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mailcow' => [
        'url'     => env('MAILCOW_API_URL'),
        'api_key' => env('MAILCOW_API_KEY'),
        'verify'  => env('MAILCOW_VERIFY_TLS', true),
        'smtp_host' => env('MAILCOW_SMTP_HOST', env('MAIL_HOST')),
        'smtp_port' => env('MAILCOW_SMTP_PORT', 587),
        'imap_host' => env('MAILCOW_IMAP_HOST', env('MAIL_HOST')),
        'imap_port' => env('MAILCOW_IMAP_PORT', 993),
    ],

    'cloudflare' => [
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'zone_id'   => env('CLOUDFLARE_ZONE_ID'),
    ],

    'mail_tenant' => [
        'parent_domain'  => env('MAIL_PARENT_DOMAIN', 'khuma.store'),
        'mail_host'      => env('MAILCOW_SMTP_HOST', env('MAIL_HOST', 'mail.khuma.store')),
        'aliases'        => ['catchall', 'bounce', 'commercial', 'campaign'],
        'inbox_local'    => env('MAIL_TENANT_INBOX', 'inbox'),
        'spf'            => env('MAIL_TENANT_SPF', 'v=spf1 mx ~all'),
        'dmarc_policy'   => env('MAIL_TENANT_DMARC', 'v=DMARC1; p=quarantine; rua=mailto:postmaster@khuma.store'),
    ],

    'uazapi' => [
        'base_url' => env('UAZAPI_BASE_URL', 'https://free.uazapi.com'),
        'admin_token' => env('UAZAPI_ADMIN_TOKEN'),
        'timeout' => env('UAZAPI_REQUEST_TIMEOUT', 30),
    ],

];
