# Codebase Structure

**Analysis Date:** 2026-04-28

## Directory Layout

```
khuma/
├── app/
│   ├── Enums/                      # Plan, Role enums
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Internal-admin controllers
│   │   │   ├── API/                # JSON webhook endpoints
│   │   │   ├── Auth/               # Breeze auth controllers
│   │   │   └── *.php               # Resource controllers (Leads, Clients, Instance, ...)
│   │   ├── Middleware/             # CheckPlanFeature
│   │   └── Requests/               # Form requests (Auth/LoginRequest, ProfileUpdateRequest)
│   ├── Jobs/                       # Queued jobs (mail, provisioning, inbound fetch)
│   ├── Livewire/
│   │   ├── Admin/                  # Admin Livewire components (Users)
│   │   ├── Subscription/           # Plan / Confirm / Checkout
│   │   └── *.php                   # CRM, messaging, settings, kanban, etc.
│   ├── Mail/                       # Mailables (CampaignEmail, LeadDirectEmail)
│   ├── Models/                     # Eloquent models
│   ├── Observers/                  # LeadObserver
│   ├── Providers/                  # AppServiceProvider
│   ├── Services/                   # MpesaService, MailcowService, CloudflareDnsService
│   ├── Support/                    # TenantMailer
│   └── View/Components/            # AppLayout, GuestLayout
├── bootstrap/
├── config/                         # Laravel config (auth, queue, mail, permission, datatables, ...)
├── database/
│   ├── factories/
│   ├── migrations/                 # Schema (see chronology below)
│   └── seeders/                    # DatabaseSeeder, InitialSeeder, PlanSeeder, CompanyClientUserSeeder
├── public/
├── resources/
│   └── views/
│       ├── admin/                  # call_logs, instance, manage, users, whatsapp
│       ├── auth/                   # Breeze auth Blade pages
│       ├── components/             # Blade UI components
│       ├── emails/                 # Email templates
│       ├── layouts/                # app, guest, navigation
│       ├── livewire/               # Livewire component templates
│       ├── profile/
│       ├── subscription/
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/                         # web.php, api.php, auth.php, console.php
├── storage/
├── tests/
│   ├── Feature/                    # Auth/, ExampleTest, ProfileTest
│   └── Unit/
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── phpunit.xml
```

## Directory Purposes

**`app/Models/`:**
- Purpose: Eloquent models (one class per domain entity)
- Files: `User.php`, `Companies.php`, `Team.php`, `Clients.php`, `Leads.php`, `LeadHistories.php`, `Notes.php`, `Messages.php`, `Instance.php`, `CallLog.php`, `EmailCampaign.php`, `EmailCampaignLog.php`, `InboundEmail.php`, `Plan.php`, `PlanFeature.php`, `PlanPrice.php`, `Subscription.php`, `SubscriptionCycle.php`, `Invoice.php`, `Payment.php`
- Convention: most models use `protected $guarded = []`; `Subscription` and `Plan` use explicit `$fillable`; `Leads` uses `SoftDeletes`

**`app/Livewire/`:**
- Purpose: Server-rendered interactive components, mounted directly as full-page routes
- Top-level: `KanbanBoard.php`, `LeadsManager.php`, `ClientList.php`, `EmailCampaigns.php`, `Settings.php`, `WhatsAppConnection.php`, `WhatsappInterface.php`, `ConnectInstance.php`, `TicketSystem.php`, `CompanyManagement.php`, `CompanyManageInteralUsers.php`
- `Admin/Users.php` — user list with role/status management
- `Subscription/ChoosePlan.php`, `Subscription/Confirm.php`, `Subscription/Checkout.php` — billing flow
- Layout attribute pattern: `#[Layout('layouts.app')]` declared on the component class

**`app/Http/Controllers/`:**
- Top-level resource controllers: `LeadsController`, `ClientsController`, `CompaniesController`, `InstanceController`, `MessagesController`, `NotesController`, `LeadHistoriesController`, `CallLogController`, `ProfileController`, `Controller` (base)
- `Admin/` — `UserController` (show, updateStatus), `DashboardController` (dashboard view + report download)
- `API/` — `MessagesController` (`POST /api/save-message` WhatsApp webhook), `PhoneCallController` (call-log ingestion)
- `Auth/` — Breeze scaffold: `AuthenticatedSessionController`, `RegisteredUserController`, `PasswordController`, `PasswordResetLinkController`, `NewPasswordController`, `ConfirmablePasswordController`, `EmailVerificationPromptController`, `EmailVerificationNotificationController`, `VerifyEmailController`

**`app/Jobs/`:**
- `SendCampaignEmails.php`, `SendLeadEmail.php`, `ProvisionTenantMailDomain.php`, `DispatchTenantInboundFetches.php`, `FetchTenantInboundMail.php`
- All implement `ShouldQueue`

**`app/Services/`:**
- `MpesaService.php` (Vodacom M-Pesa C2B), `MailcowService.php` (mailbox provisioning), `CloudflareDnsService.php` (DNS records for tenant subdomains)

**`app/Support/`:**
- `TenantMailer.php` — factory returning a per-tenant `Mailer` configured with the company's SMTP credentials

**`app/Enums/`:**
- `Plan.php`, `Role.php` (`admin`, `subscriber`, `salesperson`)

**`app/Observers/`:**
- `LeadObserver.php` — writes `LeadHistories` rows on Lead `created` and `status` change

**`app/Mail/`:**
- `CampaignEmail.php`, `LeadDirectEmail.php` — Mailables paired with `resources/views/emails/*`

**`config/`:**
- Standard Laravel: `app`, `auth`, `cache`, `database`, `filesystems`, `logging`, `mail`, `queue`, `services`, `session`
- Project-specific: `permission.php` (spatie), `datatables.php` (yajra), and `services.php` is expected to define `mail_tenant.parent_domain` (referenced from `Team::emailAddress()` and `SendCampaignEmails`)

## Migrations Chronology

`database/migrations/` (chronological — read as the timeline of feature additions):

**Foundation (2025):**
- `0001_01_01_000000_create_users_table.php` — users, password_resets, sessions
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php` — queue worker storage
- `2025_09_24_225253_create_permission_tables.php` — spatie permission tables
- `2025_09_24_225335_add_status_to_users_table.php`
- `2025_09_24_225410_create_call_logs_table.php`

**CRM core (2026-01):**
- `2026_01_24_203441_create_companies_table.php` — tenant root
- `2026_01_24_203543_create_clients_table.php`
- `2026_01_24_203613_create_leads_table.php`
- `2026_01_24_203733_create_messages_table.php`
- `2026_01_24_203835_create_lead_histories_table.php`
- `2026_01_24_203910_create_notes_table.php`
- `2026_01_24_211126_company_id.php` — adds `users.company_id` (tenant link)
- `2026_01_24_213148_create_instances_table.php` — WhatsApp instances
- `2026_01_30_201628_message_to.php` — adds `message_to` to messages
- `2026_01_30_220529_client_id.php` — adds `client_id` to messages

**Billing (2026-02):**
- `2026_02_07_111237_create_plans_table.php`
- `2026_02_07_111308_create_plan_features_table.php`
- `2026_02_07_111326_create_subscriptions_table.php`
- `2026_02_07_111346_create_subscription_cycles_table.php`
- `2026_02_07_111412_create_invoices_table.php`
- `2026_02_07_135525_create_payments_table.php`
- `2026_02_07_142031_create_plan_prices_table.php`

**Email + tenant mail + teams (2026-04):**
- `2026_04_13_000001_create_email_campaigns_table.php`
- `2026_04_13_000002_create_email_campaign_logs_table.php`
- `2026_04_21_000001_add_mail_provisioning_to_companies_table.php` — `mail_subdomain`, `mail_provision_status`, `mail_inbox_password`
- `2026_04_21_000002_create_inbound_emails_table.php`
- `2026_04_21_140128_make_sender_id_nullable_in_messages_table.php` — supports inbound messages with no internal sender
- `2026_04_21_200001_create_teams_table.php` — teams + `team_user` pivot

## Resources / Views

**`resources/views/layouts/`:**
- `app.blade.php` — authenticated app shell
- `guest.blade.php` — unauthenticated shell
- `navigation.blade.php` — top nav partial

**`resources/views/components/`:**
Blade UI primitives: `app-layout`, `application-logo`, `auth-session-status`, `dashboard-card`, `dropdown`, `dropdown-link`, `danger-button`, `primary-button`, `secondary-button`, `input-error`, `input-label`, `text-input`, `phone-input`, `prompt-input`, `modal`, `nav-link`, `responsive-nav-link`, `seo`

**`resources/views/livewire/`:**
- Top-level: `kanban-board`, `leads-manager`, `client-list`, `email-campaigns`, `settings`, `whats-app-connection`, `whatsapp-interface`, `connect-instance`, `ticket-system`, `test-ticket-system`, `company-management`, `company-manage-interal-users`
- `admin/users.blade.php`
- `subscription/{checkout,choose-plan,confirm}.blade.php`

**`resources/views/admin/`:**
- `call_logs/index.blade.php`
- `instance/{create.blade.php, connect/}`
- `manage/index.blade.php`
- `users/show.blade.php`
- `whatsapp/{index,show}.blade.php`

**`resources/views/auth/`** (Breeze): `login`, `register`, `forgot-password`, `reset-password`, `confirm-password`, `verify-email`

**`resources/views/emails/`:** `campaign.blade.php`, `campaign-text.blade.php`, `lead-direct.blade.php`, `lead-direct-text.blade.php`

**`resources/views/profile/`** — profile edit partials (Breeze)

**`resources/views/subscription/`** — `success` view (referenced by `subscription.success` route)

**Top-level views:** `dashboard.blade.php`, `welcome.blade.php`

## Routes

- `routes/web.php` — main app routes; mounts most Livewire components directly (`Route::get('/leads', LeadsManager::class)`); resource routes for `instance`, `lead`, `call_logs`; admin user routes; subscription flow; profile
- `routes/api.php` — `auth:sanctum` user endpoint, `POST /api/save-message`, `POST /api/call-logs/{id}`, `GET /api/index-all`
- `routes/auth.php` — Breeze authentication routes (login, register, password reset, email verification, logout)
- `routes/console.php` — registers the `inspire` artisan command and schedules `DispatchTenantInboundFetches` every minute (`withoutOverlapping`)

## Database Seeders

`database/seeders/`:
- `DatabaseSeeder.php` — entry point
- `InitialSeeder.php` — bootstrap roles/permissions/admin user
- `PlanSeeder.php` — plans, plan features, plan prices
- `CompanyClientUserSeeder.php` — sample tenant data for development

## Tests

`tests/`:
- `TestCase.php` — base
- `Feature/Auth/` — Breeze auth feature tests
- `Feature/ProfileTest.php`, `Feature/ExampleTest.php`
- `Unit/ExampleTest.php`
- Coverage outside Breeze scaffold is minimal

## Naming Conventions

**Files:**
- Controllers: `PascalCase` + `Controller` suffix (`LeadsController.php`)
- Models: `PascalCase`, often **plural** (`Clients`, `Leads`, `Messages`, `Notes`, `LeadHistories`, `Companies`) — non-standard for Laravel; explicit `protected $table` is sometimes set (e.g. `Leads` sets `'leads'`)
- Livewire components: `PascalCase` (`KanbanBoard.php`); paired Blade view kebab-cased (`kanban-board.blade.php`)
- Migrations: timestamp prefix + snake_case action (`create_<table>_table`, `add_<column>_to_<table>_table`)

**Directories:**
- Subnamespaces under `Controllers/` and `Livewire/` use `PascalCase` (`Admin`, `API`, `Auth`, `Subscription`)
- Blade view folders use `kebab-case` or `snake_case` (`call_logs/`, `whatsapp/`)

## Where to Add New Code

**New CRM feature (e.g. new Lead field/action):**
- Migration: `database/migrations/<timestamp>_<action>.php`
- Model update: `app/Models/Leads.php` (or related)
- UI: extend `app/Livewire/LeadsManager.php` or `app/Livewire/KanbanBoard.php` + matching `resources/views/livewire/*.blade.php`
- Audit: hook into `app/Observers/LeadObserver.php`

**New API webhook:**
- Controller: `app/Http/Controllers/API/<Name>Controller.php`
- Route: `routes/api.php` (apply `auth:sanctum` if authenticated, otherwise leave open like `/api/save-message` — beware tenant scoping)

**New Livewire full-page screen:**
- Component: `app/Livewire/<Name>.php` (use `#[Layout('layouts.app')]`)
- View: `resources/views/livewire/<kebab-name>.blade.php`
- Route: register in `routes/web.php` under the `auth` group with `Route::get('/path', <Name>::class)`

**New background job:**
- Class: `app/Jobs/<Name>.php` implementing `ShouldQueue`
- Schedule (if recurring): `routes/console.php` via `Schedule::job(...)`
- Dispatch from Livewire/Controller via `<Name>::dispatch(...)`

**New plan feature gate:**
- Add a `PlanFeature` row (seed in `PlanSeeder` or admin UI)
- Apply middleware: `->middleware('plan.feature:<feature_key>')` (register `CheckPlanFeature` alias if not yet registered)

**New external integration:**
- Service class: `app/Services/<Name>Service.php` (constructor-injected; example: `MpesaService` injected into `Confirm::pay`)
- Config keys: `config/services.php`

**New tenant-scoped table:**
- Migration must include `company_id` FK to `companies`
- Model: ensure all queries filter by `company_id` (no global scope is in place)

## Special Directories

**`storage/`:**
- Purpose: framework cache, logs, file uploads
- Generated: Yes — not committed (except `.gitignore`d structure)

**`vendor/`, `node_modules/`:**
- Purpose: composer / npm dependencies
- Committed: No

**`bootstrap/cache/`:**
- Purpose: compiled framework artifacts
- Committed: No

**`public/`:**
- Purpose: web entry (`index.php`), built assets
- Vite output written here by `npm run build`

**`kuma/`:**
- Present in repo root; appears to be project-local scratch/working directory (not part of the Laravel application)

---

*Structure analysis: 2026-04-28*
