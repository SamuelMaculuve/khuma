# Architecture

**Analysis Date:** 2026-04-28

## Pattern Overview

**Overall:** Laravel 11 monolith with Livewire 3 server-rendered components, layered MVC, and a multi-tenant SaaS model scoped by `company_id`.

**Key Characteristics:**
- Routes dispatch to either traditional Controllers (`app/Http/Controllers/`) or full-page Livewire components (`app/Livewire/`)
- Multi-tenancy enforced via `company_id` foreign keys on most domain tables (clients, leads, messages, instances, email_campaigns, teams, inbound_emails)
- RBAC via `spatie/laravel-permission` (`Spatie\Permission\Traits\HasRoles` on `User`) plus an `App\Enums\Role` enum cast on `users.role`
- Authentication scaffolded by Laravel Breeze (Blade stack); session auth for web, `auth:sanctum` for API
- Background jobs via Laravel queues; one scheduled task in `routes/console.php` (`DispatchTenantInboundFetches` every minute)
- Tenant-aware mail: per-company Mailcow subdomain provisioning + `App\Support\TenantMailer` selects per-tenant SMTP transport
- Feature gating per plan via `App\Http\Middleware\CheckPlanFeature` and `Plan::hasFeature()`

## Layers

**Routing Layer:**
- Purpose: Maps URLs to controllers and Livewire full-page components
- Location: `routes/web.php`, `routes/api.php`, `routes/auth.php`, `routes/console.php`
- Web routes are protected by the `auth` middleware group; API routes use `auth:sanctum`
- Notable: web routes mount Livewire components directly (e.g. `Route::get('/leads', LeadsManager::class)`)

**Presentation Layer (Livewire + Controllers):**
- Purpose: Handles HTTP requests, renders Blade views, drives interactive UI
- Livewire components: `app/Livewire/` (full-page CRM screens, settings, kanban, subscription flow, admin user list)
- Controllers: `app/Http/Controllers/` for resource CRUD (`LeadsController`, `InstanceController`, `CallLogController`, `ProfileController`) plus subdirectories `Admin/`, `API/`, `Auth/`
- Depends on: Models, Services, Jobs
- Used by: Routes; Blade templates in `resources/views/`

**Domain/Model Layer:**
- Purpose: Eloquent models encapsulate persistence and relationships
- Location: `app/Models/`
- Most models use `protected $guarded = []` (mass-assignment open); Subscription/Plan use explicit `$fillable`
- `Leads` uses `SoftDeletes`
- Observers: `App\Observers\LeadObserver` records `LeadHistories` rows on lead create/status-change

**Service Layer:**
- Purpose: Encapsulates third-party integrations behind reusable classes
- Location: `app/Services/`
- `MpesaService` (mobile-money payments), `MailcowService` (tenant mailbox provisioning), `CloudflareDnsService` (subdomain DNS records)
- `app/Support/TenantMailer.php` builds a per-company `Mailer` instance using tenant SMTP credentials

**Job/Queue Layer:**
- Purpose: Async work — email sending, mail provisioning, inbound mail polling
- Location: `app/Jobs/`
- Jobs: `SendCampaignEmails`, `SendLeadEmail`, `ProvisionTenantMailDomain`, `DispatchTenantInboundFetches`, `FetchTenantInboundMail`
- Queue driver configured in `config/queue.php`; the `composer dev` script runs `php artisan queue:listen --tries=1`

**Persistence Layer:**
- Purpose: Schema definitions and data access
- Location: `database/migrations/`
- Default DB driver SQLite (file at `database/database.sqlite` per `composer.json` post-create hook); MySQL/Postgres also supported via `config/database.php`

## Multi-Tenancy

**Tenant root:** `Companies` (`app/Models/Companies.php`, `companies` table)

**Scoping mechanism:**
- `users.company_id` (added in `2026_01_24_211126_company_id.php`) links each user to one company
- Domain tables carry `company_id`: `clients`, `leads`, `messages`, `instances`, `email_campaigns`, `teams`, `inbound_emails`
- Scoping is **manual / explicit** in queries — there is no global scope. Example: `app/Http/Controllers/API/MessagesController.php:39-51` looks up the tenant via `Instance.token → user.company_id`, then filters Clients/Leads by `company_id`
- Risk: any query forgetting `where('company_id', $companyId)` leaks across tenants

**Per-tenant mail provisioning:**
- `companies` carries `mail_subdomain`, `mail_provision_status`, `mail_inbox_password` (added by `2026_04_21_000001_add_mail_provisioning_to_companies_table.php`)
- `ProvisionTenantMailDomain` job creates Cloudflare DNS records and Mailcow mailbox; `TenantMailer::for($company, ...)` returns a Mailer pointed at the tenant's SMTP credentials
- `DispatchTenantInboundFetches` (scheduled every minute) fans out `FetchTenantInboundMail` per ready company

**Teams:**
- `Team` (`app/Models/Team.php`) belongs to a `Companies`, has many `User` via `team_user` pivot
- Each team can own an `email_alias` to expose a per-team inbound address `<alias>@<company.mail_subdomain>.<parent_domain>`

## Domain Modules

**CRM:**
- Models: `Clients`, `Leads`, `LeadHistories`, `Notes`
- UI: `App\Livewire\KanbanBoard` (status pipeline), `App\Livewire\LeadsManager` (list view), `App\Livewire\ClientList`
- Controllers: `LeadsController`, `ClientsController`, `LeadHistoriesController`, `NotesController`
- Lead lifecycle audit: `LeadObserver` writes a `LeadHistories` record on creation and on `status` changes

**Messaging (WhatsApp):**
- Models: `Instance` (per-user uazapi/Evolution-style WhatsApp instance, `token`-keyed), `Messages`
- UI: `App\Livewire\WhatsAppConnection`, `App\Livewire\ConnectInstance`, `App\Livewire\WhatsappInterface`, `App\Livewire\TicketSystem`
- Inbound webhook: `POST /api/save-message` → `App\Http\Controllers\API\MessagesController::saveMessage` (auto-creates `Clients` and `Leads` when an unknown phone messages a tenant instance)
- `Messages` carries `channel`, `direction` (`inbound`/`outbound`), `message_id` (dedupe), `lead_id`, `client_id`, `sender_id` (nullable since `2026_04_21_140128_make_sender_id_nullable_in_messages_table.php`)

**Email:**
- Models: `EmailCampaign`, `EmailCampaignLog`, `InboundEmail`
- Mailables: `app/Mail/CampaignEmail.php`, `app/Mail/LeadDirectEmail.php`
- Templates: `resources/views/emails/{campaign,campaign-text,lead-direct,lead-direct-text}.blade.php`
- Outbound: `App\Livewire\EmailCampaigns` enqueues `SendCampaignEmails` which chunks `Clients` (filtered by `lead_status`) and sends through `TenantMailer` when the company has provisioned mail
- Inbound: `FetchTenantInboundMail` polls IMAP, persists `InboundEmail` rows

**Billing / Subscription:**
- Models: `Plan`, `PlanFeature` (key/value features), `PlanPrice` (versioned active price), `Subscription`, `SubscriptionCycle`, `Invoice`, `Payment`
- UI: `App\Livewire\Subscription\ChoosePlan`, `App\Livewire\Subscription\Confirm`, `App\Livewire\Subscription\Checkout`
- Payment: `App\Services\MpesaService::requestPayment()` (Vodacom M-Pesa C2B); on success the `Subscription` flips to `active`
- Feature gating: `App\Http\Middleware\CheckPlanFeature` aborts 403 if `auth()->user()->subscription->plan->hasFeature($key)` is false

**Admin / RBAC:**
- Permission tables created by `2025_09_24_225253_create_permission_tables.php` (spatie)
- `User` uses `HasRoles` trait + `users.role` enum cast to `App\Enums\Role` (`admin`, `subscriber`, `salesperson`)
- Admin UI: `App\Livewire\Admin\Users` (list), `App\Http\Controllers\Admin\UserController` (show/updateStatus), `App\Http\Controllers\Admin\DashboardController` (dashboard + report download)
- Seeded via `database/seeders/InitialSeeder.php` and `CompanyClientUserSeeder.php`

**Telephony / Call logs:**
- `CallLog` model and `CallLogController` (resource, web), plus `App\Http\Controllers\API\PhoneCallController` (`POST /api/call-logs/{id}`, `GET /api/index-all`) for external phone-bridge integration

## Auth Flow

1. **Registration / login** — `routes/auth.php` (Breeze): `RegisteredUserController`, `AuthenticatedSessionController`, password reset, email verification
2. **Session** — Web routes use `web` + `auth` middleware; API uses `auth:sanctum`
3. **Tenant binding** — `users.company_id` set at registration (or by admin); all subsequent queries derive tenant from `auth()->user()->company_id`
4. **Role/permission** — Spatie roles assigned via seeder/admin; `Role` enum mirrors named roles for code-level checks
5. **Plan gating** — Feature-locked routes can apply `CheckPlanFeature:<feature_key>` middleware

## Background Jobs / Queues

**Configured:**
- Queue config: `config/queue.php` (driver per `QUEUE_CONNECTION` env)
- Jobs migration: `0001_01_01_000002_create_jobs_table.php` (database driver supported)
- Local dev runs `php artisan queue:listen --tries=1` via `composer dev`

**Jobs:**
- `SendCampaignEmails` — chunked tenant-aware bulk email with per-recipient `EmailCampaignLog`
- `SendLeadEmail` — single transactional email to a lead
- `ProvisionTenantMailDomain` — Cloudflare DNS + Mailcow mailbox setup
- `DispatchTenantInboundFetches` — scheduled fan-out (every minute, `withoutOverlapping`)
- `FetchTenantInboundMail` — IMAP polling per company

## Data Flow Examples

**Lead capture from WhatsApp:**
1. n8n (or webhook source) POSTs to `/api/save-message` with `from`, `messageId`, `message`, `instance_token`
2. `MessagesController::saveMessage` resolves `Instance` by token → `instance.user.company_id` (tenant)
3. Finds or creates `Clients` (by phone match within company), then finds or creates an open `Leads` (`status NOT IN ('won','lost')`) with `source=whatsapp`
4. Dedupes on `message_id`, then inserts `Messages` (`direction=inbound`, `channel=whatsapp`)
5. `LeadObserver::created` writes a `LeadHistories` audit row

**Lead status change (Kanban):**
1. User drags a card in `App\Livewire\KanbanBoard` (`resources/views/livewire/kanban-board.blade.php`)
2. Livewire action updates `Leads.status`
3. `LeadObserver::updated` detects `status` in `getChanges()` and inserts a `LeadHistories` row tagged `status_changed`

**Email campaign send:**
1. User builds campaign in `App\Livewire\EmailCampaigns`, persists `EmailCampaign` (filters, subject, body, company_id)
2. Component dispatches `SendCampaignEmails` job
3. Job sets `status=sending`, picks `TenantMailer::for($company, 'campaign')` if tenant mail is `ready`, else falls back to global `Mail::mailer()`
4. Chunks `Clients` (100 at a time), filtered by `lead_status` if present; one `EmailCampaignLog` per recipient (`pending → sent|failed`)
5. Final update sets `status=sent`, `sent_count`, `failed_count`, `sent_at`

**Subscription checkout (M-Pesa):**
1. `GET /subscription/plans` → `App\Livewire\Subscription\ChoosePlan` lists `Plan` + `currentPrice()`
2. `GET /subscription/payment/{plan}` → `App\Livewire\Subscription\Confirm` collects MZ phone (`84|85` regex)
3. `Confirm::pay(MpesaService)` creates a `pending` `Subscription`, calls `MpesaService::requestPayment`, records a `Payment`
4. If the M-Pesa response is successful, subscription flips to `active`; user is redirected to `/subscription/success`

## Error Handling

**Strategy:** Default Laravel exception handler; controllers/Livewire components return 4xx where appropriate (`abort(403)` in `CheckPlanFeature`, JSON 404 in `MessagesController`).

**Patterns:**
- API webhook idempotency via unique `message_id` lookup
- Job-level `failed()` hooks update the owning record's `status` to `failed` (e.g. `SendCampaignEmails::failed`)
- Per-recipient try/catch in campaign sender records `error_message` on `EmailCampaignLog` without aborting the chunk

## Cross-Cutting Concerns

**Logging:** `Illuminate\Support\Facades\Log` (Monolog via `config/logging.php`); jobs log per-recipient failures with context.

**Validation:** Inline `Request::validate()` in controllers/API; `protected $rules` on Livewire components; `App\Http\Requests\ProfileUpdateRequest` and `App\Http\Requests\Auth\LoginRequest` for form-request validation.

**Authentication:** Laravel Breeze (session) for web; Sanctum tokens for API.

**Authorization:** Spatie permissions + plan-feature middleware; tenant scoping is manual (per-query `company_id` filters).

**Tenant mail:** `App\Support\TenantMailer` swaps the mailer transport per company; fallback to global `mail` config when provisioning is not `ready`.

---

*Architecture analysis: 2026-04-28*
