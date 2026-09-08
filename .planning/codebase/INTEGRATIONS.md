# External Integrations

**Analysis Date:** 2026-04-28

## WhatsApp — uazapi (free.uazapi.com)

**Purpose:** WhatsApp messaging connectivity for tenants. Each tenant provisions a uazapi "instance" with its own token; outbound and inbound WhatsApp messages flow through uazapi.

**Configured at:**
- `.env.example:86-88` — `UAZAPI_BASE_URL=https://free.uazapi.com`
- Base URL is also hardcoded in several places (see Concerns)

**Where used:**
- `app/Http/Controllers/InstanceController.php:44` — `POST https://free.uazapi.com/instance/connect` (URL hardcoded; admin token also hardcoded at line 57: `d6ea651f-edeb-4660-88fc-16735e4d4475`)
- `app/Http/Controllers/InstanceController.php:82` — `POST https://free.uazapi.com/instance/init` to provision instance per company (admin token hardcoded line 81)
- `app/Livewire/WhatsAppConnection.php:15` — `baseUrl = 'https://free.uazapi.com'`; `connect()` (line 37), `enableWebhook()` (line 198), `checkStatus()`, `disconnect()`
- `app/Livewire/ConnectInstance.php` — alternate connection UI
- `app/Livewire/WhatsappInterface.php` — chat interface
- `app/Models/Instance.php` — stores per-tenant `token`, `status`, `profileName`, etc.

**Auth:** Per-instance bearer-style `token` header sent via `Http::withHeaders([... 'token' => $token])`.

## n8n — Inbound WhatsApp Webhook

**Purpose:** uazapi delivers incoming WhatsApp messages to an n8n workflow, which posts them back into the CRM at `POST /api/save-message`.

**Configured at:**
- `.env.example:88-89` — `N8N_WEBHOOK_ENDPOINT`, `WEBHOOK_SECRET`

**Where used:**
- `app/Livewire/WhatsAppConnection.php:198-200` — Calls uazapi `/webhook` endpoint registering `env('N8N_WEBHOOK_ENDPOINT', env('WEBHOOK_ENDPOINT', ''))` as the destination URL
- `routes/api.php:24` — `Route::post('/save-message', [MessagesController::class, 'saveMessage'])`
- `app/Http/Controllers/API/MessagesController.php:20-85` — `saveMessage()` validates `{from, messageId, message, instance_token}`, resolves tenant via `Instance::where('token', ...)`, upserts `Clients`, opens/reuses a `Leads`, writes a `Messages` row with `channel=whatsapp, direction=inbound`

## Mailcow — Per-Tenant Mail Provisioning (SMTP/IMAP)

**Purpose:** Multi-tenant email. Each company gets a subdomain mailbox provisioned in Mailcow; outbound campaigns send via tenant SMTP; inbound mail is fetched via IMAP and ingested into the CRM.

**Configured at:**
- `.env.example:67-73` — `MAILCOW_API_URL`, `MAILCOW_API_KEY`, `MAILCOW_VERIFY_TLS`, `MAILCOW_SMTP_HOST/PORT`, `MAILCOW_IMAP_HOST/PORT`
- `.env.example:62-65` — `MAIL_PARENT_DOMAIN=khuma.store`, `MAIL_TENANT_INBOX`, `MAIL_TENANT_SPF`, `MAIL_TENANT_DMARC`
- `config/services.php:38-46` — `services.mailcow`
- `config/services.php:53-60` — `services.mail_tenant` (parent domain, aliases, DMARC/SPF defaults)

**Where used:**
- `app/Services/MailcowService.php` — Wraps Mailcow Admin API: `addDomain()` (line 29), `addMailbox()` (line 47), `addAlias()` (line 60), built via `MailcowService::fromConfig()` (line 17)
- `app/Jobs/ProvisionTenantMailDomain.php` — Provisions domain + mailbox + aliases on company onboarding
- `app/Jobs/FetchTenantInboundMail.php:42-50` — Opens IMAP mailbox `{host:port/imap/ssl}INBOX` per tenant using decrypted password (`Crypt::decryptString($company->mail_inbox_password)`)
- `app/Jobs/DispatchTenantInboundFetches.php` — Fan-out scheduler for inbound fetches
- `app/Jobs/SendCampaignEmails.php:34-36` — Uses `App\Support\TenantMailer::for($company, 'campaign')` when `mail_provision_status === 'ready'`, otherwise falls back to default mailer
- `app/Models/InboundEmail.php` — Stores received messages

**Auth:** Mailcow API key in `MAILCOW_API_KEY`; per-tenant mailbox passwords stored encrypted on `companies.mail_inbox_password`.

## Cloudflare DNS — Tenant Subdomain Records

**Purpose:** Programmatically create MX/SPF/DKIM/DMARC DNS records for tenant mail subdomains under the parent zone.

**Configured at:**
- `.env.example:75-76` — `CLOUDFLARE_API_TOKEN`, `CLOUDFLARE_ZONE_ID`
- `config/services.php:48-51` — `services.cloudflare`

**Where used:**
- `app/Services/CloudflareDnsService.php` — Wraps `https://api.cloudflare.com/client/v4` (line 11). Methods: `createMx()` (line 30), built via `CloudflareDnsService::fromConfig()` (line 18)
- Invoked from `app/Jobs/ProvisionTenantMailDomain.php` during tenant mail onboarding

**Auth:** Bearer API token (`CLOUDFLARE_API_TOKEN`) scoped to a single `CLOUDFLARE_ZONE_ID`.

## M-Pesa — Subscription Payments

**Purpose:** Process subscription payments for plans via mobile money (Mozambique numbers, prefixes 84/85).

**Configured at:**
- No env vars yet (service is currently stubbed)

**Where used:**
- `app/Services/MpesaService.php:7-16` — `requestPayment(string $phone, float $amount): array` — **stub** that returns `['success' => true, 'transaction_reference' => 'MPESA-' . timestamp]`. Real API integration is a TODO (comment line 9: "Aqui entra a integração real com API M-Pesa").
- `app/Livewire/Subscription/Confirm.php:23-54` — `pay(MpesaService $mpesa)`: creates `Subscription` (pending), calls `$mpesa->requestPayment()`, creates `Payment` row (`method=mpesa`), marks subscription `active` on success, redirects to `subscription.success`
- `app/Models/Subscription.php`, `app/Models/Payment.php`, `app/Models/SubscriptionCycle.php`, `app/Models/Plan.php`, `app/Models/PlanPrice.php`, `app/Models/PlanFeature.php`, `app/Models/Invoice.php`
- Routes: `routes/web.php:43-45` — `subscription.plans`, `subscription.checkout`, `subscription.success`

**Phone validation:** `/^84|85\d{7}$/` (`Confirm.php:20`).

## AWS — Scaffolded (S3 / SES)

**Purpose:** Optional file storage (S3) and SES email driver — keys present but not actively wired.

**Configured at:**
- `.env.example:78-82` — `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_USE_PATH_STYLE_ENDPOINT`
- `config/services.php:21-25` — `services.ses` (reuses `AWS_*` keys)

**Where used:** No active code references found. Default `FILESYSTEM_DISK=local`.

## Phone Calls — Inbound Webhook

**Purpose:** Log phone calls from an external dialer/PBX.

**Where used:**
- `routes/api.php:23,25` — `POST /api/call-logs/{id}` and `GET /api/index-all` → `app/Http/Controllers/API/PhoneCallController.php`
- `app/Models/CallLog.php`, `app/Http/Controllers/CallLogController.php`

## Other Service Stubs (config only, no code use found)

`config/services.php` declares but no code consumers were found for:
- `postmark` (line 17) — `POSTMARK_TOKEN`
- `resend` (line 27) — `RESEND_KEY`
- `slack.notifications` (line 31) — `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL`

## Authentication

**Internal auth:** Laravel Breeze (session-based) — `routes/auth.php`, `app/Http/Controllers/Auth/`.
**API auth:** `auth:sanctum` middleware referenced for `/api/user` (`routes/api.php:18`); inbound webhooks (`/api/save-message`, `/api/call-logs/{id}`) are currently **unauthenticated** at the route level.

## Environment Configuration Summary

**Required env vars for full functionality:**
- App: `APP_KEY`, `APP_URL`, `DB_CONNECTION`
- WhatsApp: `UAZAPI_BASE_URL`, `N8N_WEBHOOK_ENDPOINT`, `WEBHOOK_SECRET`
- Mailcow: `MAILCOW_API_URL`, `MAILCOW_API_KEY`, `MAILCOW_SMTP_HOST/PORT`, `MAILCOW_IMAP_HOST/PORT`
- Mail tenant: `MAIL_PARENT_DOMAIN`, `MAIL_TENANT_INBOX`, `MAIL_TENANT_SPF`, `MAIL_TENANT_DMARC`
- Cloudflare: `CLOUDFLARE_API_TOKEN`, `CLOUDFLARE_ZONE_ID`
- Optional: `AWS_*`, `REDIS_*`, `POSTMARK_TOKEN`, `RESEND_KEY`, `SLACK_*`

**Secrets location:** `.env` (not committed). `.env.example` enumerates required keys.

## Webhooks

**Incoming (CRM receives):**
- `POST /api/save-message` — uazapi → n8n → CRM (WhatsApp inbound) — `app/Http/Controllers/API/MessagesController.php`
- `POST /api/call-logs/{id}` — external phone system → CRM — `app/Http/Controllers/API/PhoneCallController.php`

**Outgoing (CRM emits):**
- Registers `N8N_WEBHOOK_ENDPOINT` with uazapi via `POST {uazapi}/webhook` (`app/Livewire/WhatsAppConnection.php:198`)

---

*Integration audit: 2026-04-28*
