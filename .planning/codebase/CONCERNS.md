# Codebase Concerns

**Analysis Date:** 2026-04-28

## Tech Debt

**Multi-tenant scoping (manual, not enforced):**
- Issue: No global scope or trait for tenant isolation. Every query manually applies `->where('company_id', auth()->user()->company_id)`. A single forgotten filter leaks cross-tenant data.
- Files: `app/Livewire/EmailCampaigns.php` (8 manual filters), `app/Livewire/KanbanBoard.php:126,160,301`, `app/Livewire/ClientList.php:56,70,83,100`, `app/Http/Controllers/Admin/DashboardController.php:18-65`, `app/Http/Controllers/API/MessagesController.php`
- Impact: High risk of cross-tenant data leakage. Models like `Leads`, `Clients`, `Messages`, `EmailCampaign` have NO global scope. `Companies` model has no `BelongsToCompany` trait.
- Fix approach: Introduce a `BelongsToCompany` trait with an `addGlobalScope` keyed off `auth()->user()->company_id`; auto-assign `company_id` in a `creating` event. Apply to `Leads`, `Clients`, `Messages`, `Notes`, `LeadHistories`, `EmailCampaign`, `EmailCampaignLog`, `Instance`, `InboundEmail`.

**Plural model class names violate Laravel conventions:**
- Issue: Models named `Leads`, `Clients`, `Companies`, `Messages`, `Notes`, `LeadHistories` (plural) instead of singular. Each must hardcode `protected $table = 'leads'` or rely on Eloquent guessing `leads → leads` (works), but `LeadHistories → lead_histories` is correct by accident.
- Files: `app/Models/Leads.php`, `Clients.php`, `Companies.php`, `Messages.php`, `Notes.php`, `LeadHistories.php`
- Impact: Confuses every relationship (`belongsTo(Clients::class)` reads wrong); prevents future contributors from following conventions; eventual collisions with auto-discovery (model factories, route model binding pluralization).
- Fix approach: Rename classes to `Lead`, `Client`, `Company`, `Message`, `Note`, `LeadHistory`. Update all references via global search-replace.

**Mass assignment wide open:**
- Issue: Every domain model uses `protected $guarded = []` instead of a whitelist `$fillable`.
- Files: `app/Models/Leads.php:15`, `Clients.php:10`, `Companies.php:11`, `Messages.php:9` and others
- Impact: Any user-controllable input passed to `create()` / `update()` can set arbitrary columns including `company_id`, `user_id`, `status`, foreign keys — privilege escalation and tenant escape vectors.
- Fix approach: Replace `$guarded = []` with explicit `$fillable` arrays per model.

**Migration hygiene — single-column patches:**
- Issue: Schema evolved through one-off migrations with non-descriptive names. `2026_01_24_211126_company_id.php`, `2026_01_30_201628_message_to.php`, `2026_01_30_220529_client_id.php`, `2026_04_21_140128_make_sender_id_nullable_in_messages_table.php`.
- Files: `database/migrations/`
- Impact: Hard to reconstruct schema from history; suggests the schema was unstable at MVP time. Risk of drift between dev DB (sqlite per `.env.example`) and any production DB.
- Fix approach: Squash into a baseline schema migration before first production deploy.

**i18n absent — mixed PT/EN literals:**
- Issue: User-facing strings hardcoded in Portuguese (`'todos'`, `'Pendentes'`, `'Funcionalidade indisponível no seu plano'`) interleaved with English (`'new'`, `'won'`, `'lost'`, `'Contacto WhatsApp'`).
- Files: `app/Livewire/KanbanBoard.php:27,82-86,270,277`, `app/Http/Middleware/CheckPlanFeature.php:25`, `app/Http/Controllers/API/MessagesController.php:62`
- Impact: No `lang/` directory exists. Cannot localize. Status enums like `'todos'` mixed with `'new'`/`'won'` are inconsistent.
- Fix approach: Create `lang/pt/` and `lang/en/`, replace literals with `__()` helpers, set `APP_LOCALE=pt` if PT is primary.

**Subscription/billing — payment gateway is a stub:**
- Issue: `MpesaService::requestPayment()` returns hardcoded `success: true` with a fake transaction reference.
- Files: `app/Services/MpesaService.php:6-15`, `app/Livewire/Subscription/Confirm.php:38` (calls stub), `app/Livewire/Subscription/Checkout.php:26` (`// Aqui você liga Stripe, PayPal, etc`)
- Impact: Subscriptions activate without payment. Anyone hitting `/subscription/payment/{plan}` with a valid phone regex auto-receives an active subscription.
- Fix approach: Implement actual M-Pesa C2B/STK push, verify callback signature, only mark `paid` after callback. Keep status `pending` until confirmed.

**Frontend SSR-only:**
- Issue: Livewire + Blade + (likely) DataTables. No SPA layer, no API for external consumers beyond 3 webhook endpoints.
- Impact: Mobile/native client cannot reuse business logic; full page reload model couples views tightly to backend.
- Improvement path: Acceptable for current scope; document as constraint rather than fix.

## Known Bugs

**Modified-but-uncommitted working tree:**
- Files modified per `git status`:
  - `.env.example`
  - `app/Http/Controllers/API/MessagesController.php`
  - `app/Livewire/KanbanBoard.php`
  - `app/Livewire/WhatsAppConnection.php`
  - `resources/views/components/app-layout.blade.php`
  - `resources/views/layouts/app.blade.php`
  - `resources/views/livewire/kanban-board.blade.php`
  - `resources/views/livewire/settings.blade.php`
- Risk: Eight files in flight on `Lead_dev`. Latest commit `f0eb900 New layout and email implementation` predates these. Must be reviewed and committed or discarded before any new phase to keep mapping accurate.

**`Messages::messages()` self-relation looks wrong:**
- Symptom: `Messages` model defines `messages() { return $this->hasMany(Messages::class, 'sender_id'); }` and `instance()` also keys off `sender_id`. Same column treats `Messages` as both `User` and `Instance`.
- Files: `app/Models/Messages.php:18-29`
- Trigger: Eager-loading `instance` after sender returns wrong rows; outbound vs inbound disambiguation unreliable.
- Workaround: None. Needs schema clarification (separate `instance_id` column).

## Security Considerations

**API routes unauthenticated:**
- Risk: `routes/api.php` exposes `POST /api/call-logs/{id}`, `POST /api/save-message`, `GET /api/index-all` with NO auth middleware. Only `/user` is gated by `auth:sanctum`.
- Files: `routes/api.php:21-23`
- Current mitigation: `MessagesController::saveMessage` validates an `instance_token` body field against `Instance` table — partial token-based auth, but accepted as plain body field, no signature.
- Recommendations: 
  - Move webhook routes to a dedicated `webhooks/` group
  - Validate the `WEBHOOK_SECRET` env (currently empty in `.env.example`) via signed header (`X-Webhook-Signature`) using `hash_equals`
  - Apply `throttle:` middleware
  - `GET /api/index-all` returns ALL call logs (no scoping shown) — must be auth + tenant-scoped or removed.

**Webhook signature validation absent:**
- Risk: `WEBHOOK_SECRET=` is declared in `.env.example` but `grep` finds zero usages of `WEBHOOK_SECRET`, `hash_equals`, or `signature` anywhere in `app/` or `routes/`.
- Files: `routes/api.php`, `app/Http/Controllers/API/MessagesController.php`
- Current mitigation: Only `instance_token` lookup.
- Recommendation: Verify HMAC of payload on every n8n/uazapi callback.

**No rate limiting:**
- Risk: API webhook endpoints are not throttled; a leaked `instance_token` allows unlimited message injection / DB growth.
- Files: `routes/api.php`
- Recommendation: `Route::middleware('throttle:60,1')`.

**RBAC adopted but not enforced on routes:**
- Risk: Spatie's `HasRoles` is on `User` and a single `hasRole('admin')` check exists in `app/Http/Controllers/CallLogController.php:55`. No `role:` or `permission:` middleware on any route in `routes/web.php`. Admin-only screens (`/users`, `/manage`, `CompanyManagement`) rely only on `auth`.
- Files: `routes/web.php:25-46`, `app/Models/User.php:10`
- Recommendation: Apply `Route::middleware(['auth','role:admin'])->group(...)` around admin routes; add Gates for tenant-scoped policies.

**Mass assignment via `$guarded = []`:** see Tech Debt above.

**Bootstrap kernel has no global middleware customization:**
- Files: `bootstrap/app.php` — `withMiddleware(function (Middleware $m) { // })` is empty.
- Risk: No tenant context middleware, no security headers (CSP, HSTS, X-Frame-Options), no force-HTTPS in production.
- Recommendation: Add `TrustProxies`, security headers, and a `SetTenantContext` middleware.

**`.env.example` completeness gaps:**
- Files: `.env.example`
- Issues:
  - `WEBHOOK_SECRET=` empty — referenced nowhere in code, but should be required and used.
  - `MAILCOW_API_KEY=`, `CLOUDFLARE_API_TOKEN=`, `CLOUDFLARE_ZONE_ID=`, `AWS_ACCESS_KEY_ID=` empty without comments documenting where to obtain them.
  - First line is a stray shell command (`php artisan db:seed --class=EmployeeSeeder`) at the top of the file — will break `php artisan key:generate`/parsing tools that read the file as dotenv.
  - No `MPESA_*` vars although `MpesaService` exists (currently a stub, but real impl will need credentials).
  - No `UAZAPI_TOKEN` / per-tenant guidance.
  - Default `DB_CONNECTION=sqlite` and `MAIL_MAILER=log` are dev defaults — needs a production `.env.production.example`.

## Performance Bottlenecks

**Synchronous M-Pesa request blocks Livewire request:**
- Problem: `Confirm::pay()` calls external payment API in-request; UX freezes during HTTP round-trip.
- Files: `app/Livewire/Subscription/Confirm.php:24-53`
- Cause: No queue dispatch; redirect happens after sync external call.
- Improvement: Initiate STK push asynchronously, render a polling/waiting state, finalize on callback webhook.

**Inbound message ingestion is synchronous:**
- Problem: `MessagesController::saveMessage` does multiple lookups + creates Client/Lead/Message inside the HTTP request.
- Files: `app/Http/Controllers/API/MessagesController.php`
- Cause: Logic should be a queued job; webhook should respond `202` immediately.
- Improvement: Dispatch a `ProcessInboundWhatsAppMessage` job; return `202 Accepted`.

**`whereHas` on dashboard counts:**
- Problem: `Messages::whereHas('lead', fn($q) => $q->where('company_id', $companyId))->count()` runs subquery every dashboard load.
- Files: `app/Http/Controllers/Admin/DashboardController.php:32`
- Cause: `Messages` table missing direct `company_id` (denormalized) and no caching layer.
- Improvement: Denormalize `company_id` onto `messages` table or cache count for 5 minutes.

## Fragile Areas

**`Messages.sender_id` overloaded:**
- Files: `app/Models/Messages.php:18-29`
- Why fragile: Same column doubles as `User` foreign key (outbound) and `Instance` foreign key (inbound). The migration `make_sender_id_nullable_in_messages_table.php` confirms this is being patched around.
- Safe modification: Add separate `instance_id` column; backfill; drop overload.

**Kanban state machine in Livewire component:**
- Files: `app/Livewire/KanbanBoard.php` (~300 lines, modified)
- Why fragile: Status names are partly Portuguese (`'Pendentes'`), partly English (`'new'`, `'won'`, `'lost'`); mixed enums in same component.
- Safe modification: Centralize statuses in a `LeadStatus` enum (PHP 8.1 enums) and reference everywhere.

## Scaling Limits

**SQLite default:**
- Current capacity: `DB_CONNECTION=sqlite` ships in `.env.example`.
- Limit: Single-writer; locks under concurrency; not multi-tenant production grade.
- Scaling path: Switch to MySQL/Postgres with `DB_*` block uncommented before production.

**Queue driver = database:**
- Current: `QUEUE_CONNECTION=database` in `.env.example`.
- Limit: Polling cost on jobs table grows; not ideal for high-volume email/WhatsApp ingestion.
- Scaling path: Move to Redis/Horizon when `EmailCampaign` volumes grow.

## Dependencies at Risk

**Mailcow + Cloudflare provisioning per-tenant:**
- Risk: `ProvisionTenantMailDomain`, `MailcowService`, `CloudflareDnsService` couple the platform to two specific external systems. Failure modes (DNS propagation, Mailcow API errors) appear to throw inside a queued job — retry policy unclear.
- Files: `app/Jobs/ProvisionTenantMailDomain.php`, `app/Services/MailcowService.php`, `app/Services/CloudflareDnsService.php`
- Migration plan: Abstract behind an interface `MailProvisioner` so alternate providers (AWS SES + Route53) can swap.

## Missing Critical Features

**Email deliverability management UI:**
- Problem: SPF / DKIM / DMARC strings are set per-tenant during provisioning (`MAIL_TENANT_SPF`, `MAIL_TENANT_DMARC` in `.env.example`) but no UI surfaces verification status, DKIM key rotation, or DMARC reports.
- Blocks: Customer self-service when their DNS isn't propagating, deliverability monitoring, reputation tracking.

**System-wide audit log:**
- Problem: Only `LeadHistories` exists, scoped to lead changes. No audit trail for user logins, role changes, subscription edits, company settings, payment events, mail provisioning.
- Blocks: Compliance, incident forensics, support investigations.

**Documented `.env` requirements for integrations:**
- Problem: No `README` section or doc covers required env vars for WhatsApp (`UAZAPI_BASE_URL`, per-instance tokens), Mailcow, Cloudflare, AWS, M-Pesa.
- Blocks: Onboarding new developers; production deployments.

**API documentation:**
- Problem: Three `/api/*` endpoints exist with no OpenAPI/Postman documentation.
- Blocks: External integrators (n8n flows, mobile clients).

**Tenant context middleware:**
- Problem: No middleware sets a tenant context globally; every controller/component queries `auth()->user()->company_id` ad hoc.
- Blocks: Defense-in-depth against the multi-tenant integrity issues above.

## Test Coverage Gaps

**Near-zero domain test coverage:**
- What's not tested: `tests/` contains only Laravel's default Breeze auth tests (`tests/Feature/Auth/*`) plus stock `ExampleTest.php` files. Zero tests cover Leads, Clients, Messages, Kanban, EmailCampaigns, Subscriptions, M-Pesa, WhatsApp ingestion, mail provisioning, multi-tenant isolation.
- Files: `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`, `tests/Feature/ProfileTest.php`, `tests/Feature/Auth/*` (only)
- Risk: Any change to scoping, payment, webhook handling, or job logic can silently break tenants. No regression protection.
- Priority: **High**. Minimum first targets:
  - Tenant isolation: Lead / Client / Message scoped query test
  - Webhook signature: invalid token rejected
  - Subscription stub: payment failure path
  - Job: `SendCampaignEmails` retry behavior
  - RBAC: non-admin denied on `/users`, `/manage`

**No CI configured:**
- What's not tested: No `.github/workflows/`, no GitLab CI file detected. Tests are not automatically run on push.
- Risk: Even existing tests can rot.
- Priority: Medium. Add GitHub Actions running `php artisan test` + linter.

---

*Concerns audit: 2026-04-28*
