# Technology Stack — v1.0 Complete CRM Additions

**Project:** Khuma CRM (brownfield, Laravel 11 / Livewire 3 / PHP 8.2)
**Researched:** 2026-04-28
**Scope:** ONLY new packages required for v1.0 capabilities. Existing stack (Laravel 11.31, Livewire 3.6, Spatie Permission 6.21, Yajra DataTables 11.0, Sanctum, Breeze, Tailwind 3, Vite 6, M-Pesa, Mailcow, Cloudflare DNS) is taken as given and NOT re-researched.

## Hard Constraints That Drove Choices

- Laravel **11.31** (NOT 12/13). Eliminates packages that bumped to require `^12.0` (most notably `spatie/laravel-activitylog` v5 and the official `laravel/ai` SDK which needs PHP 8.4 + Laravel 12).
- PHP **8.2** (NOT 8.4). Same exclusion logic.
- Server-rendered Livewire UI — JS-heavy SPA charting libs are out.
- Multi-tenant scoping is manual and being hardened — every package added must be scopable by `company_id`, not introduce its own implicit global tenancy.
- Queue is `database` driver. Anything async must enqueue cleanly without Redis as a hard requirement.

---

## Recommended Stack (Net New Adds)

### 1. Deals / Opportunities + Forecasting

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| `brick/money` | `^0.13` | Currency-correct money math (allocate, convert, format) | CRM forecasts must not lose cents to float drift; multi-currency on the roadmap. ISO-4217 aware, immutable value object. |
| `nnjeim/world` | `^1.1` (composer require nnjeim/world) | Currency + country reference data | Seed currency selector for Deals UI without hand-typing ISO codes. Optional — can be replaced with `symfony/intl`. |

**Storage pattern (no package):** Store money as two columns per amount: `amount_minor BIGINT` (integer minor units, e.g. cents) + `currency CHAR(3)`. Cast to `Brick\Money\Money` via a custom Eloquent cast. This is the canonical Brick\Money pattern and avoids `DECIMAL(15,4)` rounding ambiguity.

**Explicitly NOT adding:**
- `cknow/laravel-money` / `akaunting/laravel-money` — wrap MoneyPHP, not Brick\Money; thinner currency math.
- `elegantly/laravel-money` and `finller/laravel-money` — convenience wrappers around Brick\Money but pin an older `^0.11` brick/money. Skip the wrapper, use Brick directly via a thin custom cast (~30 lines).
- Any `DECIMAL(15,4)` "just store a decimal" approach — rejected; loses currency semantics, breaks under multi-currency forecasting.

### 2. Tasks / Activities + Calendar 2-Way Sync

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| `laravel/socialite` | `^5.x` | OAuth2 login flow for Google + Microsoft | First-party Laravel package; already the standard for "Connect your Google account"-style flows. Provides token exchange + refresh primitives. |
| `socialiteproviders/microsoft-azure` | `^5.x` | Adds Azure AD / Microsoft 365 provider to Socialite | Microsoft is not in core Socialite; this is the canonical community add-on. |
| `google/apiclient` | `^2.18` | Google Calendar API v3 client | Direct SDK; gives full control over watch channels (push notifications) and incremental sync tokens — both required for true 2-way sync. |
| `microsoft/microsoft-graph` | `^2.x` | Microsoft Graph SDK (events, calendars, subscriptions) | Official MS SDK; needed for `/me/events` + change-notifications subscription. |
| `nesbot/carbon` | already in Laravel | RRULE / timezone arithmetic | — |
| `simshaun/recurr` | `^5.x` | RRULE expansion (RFC 5545) for recurring events | Both Google and MS return RRULE strings; `recurr` is the de-facto PHP RRULE expander. Needed if you display recurring instances inside Khuma's own calendar grid. |

**Architectural note for 2-way sync:** Use Google's `syncToken` + `events.watch` push channels and Microsoft Graph `subscriptions` (webhooks) for change notifications. Fall back to `withoutOverlapping` scheduled poll jobs (you already use this pattern in `DispatchTenantInboundFetches`). Store provider-side `etag` and `eventId` on the local `Activity` model to resolve conflicts.

**Explicitly NOT adding:**
- `spatie/laravel-google-calendar` (`^3.8.4`) — uses a **single service account** with one calendar config. It is the *wrong* model for multi-tenant per-user OAuth. It is excellent for "company has one calendar" use cases; it does NOT fit Khuma where each salesperson connects their own Google account.
- `dcblogdev/laravel-microsoft-graph`, `lloadout/microsoftgraph`, `prasadchinwal/microsoft-graph` — convenience wrappers; all add a layer over the official SDK that has to be re-learned, and most pin older Graph versions. Use `microsoft/microsoft-graph` directly.
- `spatie/icalendar-generator` — only useful if exporting `.ics`; not needed for true 2-way sync.

### 3. Custom Fields + CSV Import / Export + Dedupe

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| `spatie/laravel-schemaless-attributes` | `^2.x` | JSON-column-backed schemaless attributes on Lead/Deal/Contact | Per-tenant custom fields without per-tenant migrations or EAV. Index-friendly on MySQL JSON. |
| `spatie/laravel-tags` | `^4.x` | Polymorphic tags on any model | Standard. Plays well with `company_id` scoping if you add it on the pivot. |
| `maatwebsite/excel` (Laravel Excel) | `^3.1.68` | CSV / XLSX import + export with chunked queueable imports | Battle-tested; `WithChunkReading` + `ShouldQueue` lets large imports use the existing queue worker. |
| `league/csv` | `^9.x` | Streamed CSV parsing for the import preview/mapping step | Lower-overhead than booting Laravel-Excel just to read 100-row preview before mapping columns. |

**Custom fields decision: JSON, NOT EAV.**
- A dedicated `custom_field_definitions` table per `company_id` (label, key, type, options, required, order)
- An `extra_attributes` JSON column on `leads`, `deals`, `clients` driven by `spatie/laravel-schemaless-attributes`
- Validation derived dynamically from the definitions table at request time

This avoids EAV's join explosion, supports per-tenant divergence, and is queryable via MySQL `->>` operators.

**Dedupe:** No package — implement in-app:
- Phone: normalize via `giggsey/libphonenumber-for-php` (`^9.x`) before storing, then unique key + similarity match.
- Email: lowercase + plus-stripping, then unique key.
- Name: `soundex()` + `levenshtein()` (PHP built-ins) for fuzzy match in a "Possible duplicates" admin view.

**Explicitly NOT adding:**
- `eav/laravel-eav` and similar EAV packages — performance trap, query mess, well-known anti-pattern.
- `rappasoft/laravel-livewire-tables` for import preview — overkill; existing Yajra DataTables pattern + a Livewire wizard is enough.
- A separate dedupe library — none worth the ceremony for the volumes Khuma is targeting.

### 4. Workflow Automation Engine

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| **Build in-house** on `Illuminate\Events` + Laravel queues | n/a | Trigger / condition / action engine, drip sequences, outbound webhooks | The existing `LeadObserver` + jobs pattern already approximates this. Persist `Workflow` / `WorkflowStep` / `WorkflowRun` Eloquent models; dispatch a `RunWorkflowStep` job per step with `delay()` for drip. PROJECT.md key decision already commits to in-app, not separate service. |
| `symfony/expression-language` | `^7.x` (already a Laravel transitive) | Sandboxed expression evaluation for conditions (`lead.value > 1000 and lead.source == 'whatsapp'`) | Already shipped with Symfony; pulls in zero new vendor weight. Safer than `eval()`-style PHP, more expressive than a hand-rolled comparator. |
| `spatie/laravel-webhook-server` | `^3.x` | Outbound webhooks with retries + signing (HMAC) | The "send a webhook" action node. Queueable, signed, retry policy built-in. Matches Khuma's queue architecture. |

**Why NOT a generic rule engine:**
- `symfony/workflow` (and the `zerodahero/laravel-workflow` / `brexis/laravel-workflow` wrappers) model **state machines** — Lead/Deal stage transitions. They do NOT model trigger→condition→action automation. Wrong abstraction. **Keep it in mind only if you later formalize Deal stage transitions** as a state machine — that *is* a fit. For v1.0 automation, build the engine.
- `ruler` / `hoa/ruler` / other PHP rule engines — abandoned or low-traction. Adopting them is more risk than building 400 LOC of trigger registry + action dispatcher.

**Explicitly NOT adding:**
- A separate orchestrator (Temporal, n8n embedded, `laravel-durable-workflow`) — explicitly out of scope per PROJECT.md.
- `spatie/laravel-event-sourcing` — overkill for trigger/action; reserve for if/when you need full auditable Deal history replay.

### 5. Reporting & Analytics Dashboards

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| **ApexCharts** (CDN or npm `apexcharts ^4.x`) | `^4.x` | Funnel, bar, line, donut charts | Best-looking out of the box, good Livewire integration patterns documented, MIT, mature. |
| `asantibanez/livewire-charts` | `^3.x` | Thin Livewire wrapper over ApexCharts | Optional convenience — use only if the team prefers component-based charts over a hand-rolled `wire:ignore` + Alpine pattern. Adds zero JS bundle weight beyond ApexCharts. |
| (in-house) Eloquent aggregates → DTOs → ApexChart series | n/a | Funnel, win/loss, conversion, MRR/ARR/churn calculations | Pure SQL + Carbon. No package. |

**Why NOT Filament widgets:**
- Filament is a full admin panel. Khuma's UI is custom Livewire + Tailwind + Breeze. Adopting Filament widgets means adopting Filament's panel scaffolding, theming, and dependency tree. Wrong fit for a brownfield app with its own design system.

**Explicitly NOT adding:**
- Chart.js — viable but ApexCharts has nicer defaults for the funnel/donut/win-loss visualizations a CRM needs and is the more common pairing with Livewire in 2026 community examples.
- Echarts / Highcharts — Highcharts is non-free for commercial; Echarts is heavier and less idiomatic in the Livewire ecosystem.
- `laravel/pulse` for business analytics — Pulse is for app *operational* telemetry (queue, slow queries), NOT sales reporting. Consider it later as an ops tool but it does not satisfy this requirement.

### 6. Secondary Capabilities

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| `pragmarx/google2fa-laravel` | `^2.x` | TOTP 2FA with middleware | Lightweight, drop-in middleware, fits the existing Breeze auth — no need to swap to Fortify. |
| `bacon/bacon-qr-code` | `^3.x` | QR code generation for 2FA enrollment | Standard companion to google2fa. |
| `laravel/socialite` | `^5.x` | SSO (Google, Microsoft) | Same install used for calendar OAuth — single dependency does double duty. |
| `socialiteproviders/microsoft-azure` | `^5.x` | MS SSO provider | — |
| `owen-it/laravel-auditing` | `^14.0.2` | System-wide audit log | **Required choice** for Laravel 11 — `spatie/laravel-activitylog` v5 requires Laravel 12+. owen-it is purpose-built for "before/after" model auditing which is exactly what an audit trail needs (vs activitylog which is more "user did action X"). |
| `knuckleswtf/scribe` | `^5.9` | API docs from controllers + form requests | Generates HTML + OpenAPI 3.1 + Postman from existing Sanctum API; no annotations required for the simple cases. |
| `laravel/reverb` | `^1.x` | First-party WebSocket server (Pusher protocol) | In-app real-time notifications without paying Pusher; Livewire 3 has native echo integration via `#[On('echo:...')]` attribute. |
| `laravel/echo` (npm) | `^1.16+` | Browser-side WebSocket client | Required for Reverb. |
| (built-in) `Illuminate\Notifications` | n/a | Database + mail + broadcast notifications | Covers in-app bell-icon notifications via the `database` channel + Reverb broadcast. No package needed. |
| `openai-php/laravel` | `^0.10+` | OpenAI client for AI assist (summaries, next-best-action) | Mature, idiomatic Laravel facade. |
| `anthropic-ai/sdk` (composer: `anthropic-ai/sdk`) — *use the official PHP SDK if available, else direct HTTP via `Illuminate\Http\Client`* | latest | Anthropic Claude client | Anthropic's PHP SDK ecosystem is younger; if no stable Laravel-friendly SDK is on Packagist when you implement, use `Http::withHeaders(...)->post(...)` directly — the API is small. |
| `mcamara/laravel-localization` | `^2.x` | URL-prefixed locale routing | Khuma is PT-primary; this is the standard package. |
| `spatie/laravel-translation-loader` | `^2.x` | DB-backed translations (admin-editable strings) | Use ONLY if you need non-developers to edit translations. Otherwise stick to `lang/*.json` files. |

**Explicitly NOT adding for secondary:**
- `laravel/fortify` — Fortify replaces Breeze's controllers; Khuma already has Breeze + custom Livewire screens. Bolting Fortify on for 2FA alone is destabilizing. Use `pragmarx/google2fa-laravel` middleware on top of existing Breeze.
- `spatie/laravel-activitylog` — requires Laravel 12. Re-evaluate post-Laravel-12 upgrade.
- `laravel/ai` (official AI SDK) — requires PHP 8.4 + Laravel 12. Re-evaluate later.
- `pusher/pusher-php-server` — Reverb is Pusher-protocol compatible and self-hosted; do not pay Pusher.
- `darkaonline/l5-swagger` — Scribe is strictly better DX in 2026; only use Swagger if you must hand-author OpenAPI.

---

## Alternatives Considered (Decision Table)

| Decision | Recommended | Alternative | Why Not |
|----------|-------------|-------------|---------|
| Money library | `brick/money` | `moneyphp/money` | Brick is the modern fork; better PHP 8 typing, immutable, ISO-current. |
| Money cast | Custom cast (~30 LOC) | `elegantly/laravel-money`, `finller/laravel-money` | Both pin older brick/money 0.11; trivial to roll your own. |
| Calendar (Google) | Direct `google/apiclient` | `spatie/laravel-google-calendar` | Spatie pkg = single service account, wrong for per-user OAuth. |
| Calendar (Microsoft) | Direct `microsoft/microsoft-graph` SDK | `dcblogdev/laravel-microsoft-graph` etc. | Wrappers add abstraction without value, lag SDK versions. |
| Custom fields | `spatie/laravel-schemaless-attributes` (JSON) | EAV tables | EAV is an anti-pattern; JSON columns are queryable + index-friendly in MySQL 8. |
| Workflow engine | Build in-house on Events + Queues | `symfony/workflow` | Wrong abstraction (state machine ≠ trigger/action). |
| Charts | ApexCharts | Chart.js, Filament widgets | ApexCharts has best CRM-shaped chart defaults; Filament wrong UI paradigm. |
| Audit log | `owen-it/laravel-auditing` | `spatie/laravel-activitylog` | Spatie v5 requires Laravel 12; we're on 11. |
| 2FA | `pragmarx/google2fa-laravel` | `laravel/fortify` | Fortify rebases auth; we have Breeze. |
| Real-time | `laravel/reverb` | Pusher cloud / Soketi | First-party, free, Pusher-protocol compatible. |
| AI client | `openai-php/laravel` + raw HTTP for Anthropic | `laravel/ai` SDK | Official SDK requires Laravel 12 + PHP 8.4. |

---

## Installation (One-Shot Reference)

```bash
# Deals / forecasting
composer require brick/money:^0.13

# Calendar sync
composer require laravel/socialite socialiteproviders/microsoft-azure
composer require google/apiclient:^2.18
composer require microsoft/microsoft-graph:^2
composer require simshaun/recurr:^5
composer require giggsey/libphonenumber-for-php:^9

# Custom fields + import/export + tags
composer require spatie/laravel-schemaless-attributes
composer require spatie/laravel-tags
composer require maatwebsite/excel:^3.1.68
composer require league/csv:^9

# Workflow / webhooks
composer require spatie/laravel-webhook-server

# Audit log
composer require owen-it/laravel-auditing:^14

# 2FA
composer require pragmarx/google2fa-laravel
composer require bacon/bacon-qr-code:^3

# API docs
composer require --dev knuckleswtf/scribe:^5.9

# Real-time
composer require laravel/reverb
php artisan install:broadcasting

# AI
composer require openai-php/laravel

# i18n
composer require mcamara/laravel-localization
# (translation-loader only if DB-backed translations are needed)

# Frontend
npm install apexcharts
npm install laravel-echo pusher-js
```

---

## Integration Notes (Livewire / Queue / Multi-Tenant)

- **Queue:** All long-running jobs (calendar push subscription renewals, workflow step execution, CSV imports, webhook sends) must `implements ShouldQueue` and use the existing `database` queue connection. They must accept `companyId` in the constructor — never rely on `auth()->user()` inside a job.
- **Livewire 3:** ApexCharts requires a `wire:ignore` wrapper on the chart container plus a `Livewire.on(...)` listener to update series data — confirmed working pattern in 2026 community guides.
- **Reverb broadcasting:** Use the `#[On('echo:private-company.{companyId},*')]` Livewire attribute for per-tenant channels. Bind tenant channel auth in `routes/channels.php` via `company_id` check.
- **Audit log scoping:** `owen-it/laravel-auditing` writes to a global `audits` table — add `company_id` via the package's `resolveAdditionalData()` hook so per-tenant audit views can filter cleanly.
- **Schemaless attributes scoping:** `extra_attributes` is on the model — already tenant-scoped because the parent row is.
- **Mass-assignment risk:** Custom fields land in `extra_attributes` JSON; ensure the controller / Livewire validation rebuilds the rules from `custom_field_definitions` rather than blindly merging request data — relevant to the open `$guarded = []` audit item in PROJECT.md.

---

## Sources

- [brick/money on Packagist](https://packagist.org/packages/brick/money) — v0.13.0, 2026-03-28
- [maatwebsite/excel on Packagist](https://packagist.org/packages/maatwebsite/excel) — v3.1.68, 2026-03-17
- [spatie/laravel-google-calendar (single service account model)](https://github.com/spatie/laravel-google-calendar)
- [microsoft/microsoft-graph on Packagist](https://packagist.org/packages/microsoft/microsoft-graph)
- [Laravel AI SDK requirements (PHP 8.4 + Laravel 12)](https://laravel.com/docs/13.x/ai-sdk)
- [owen-it/laravel-auditing v14.0.2](https://packagist.org/packages/owen-it/laravel-auditing)
- [spatie/laravel-activitylog v5 — requires Laravel 12](https://packagist.org/packages/spatie/laravel-activitylog)
- [knuckleswtf/scribe v5.9.0, 2026-03-21](https://github.com/knuckleswtf/scribe)
- [pragmarx/google2fa-laravel — last update 2026-04-17](https://packagist.org/packages/pragmarx/google2fa-laravel)
- [Laravel Reverb docs](https://reverb.laravel.com/)
- [Spatie schemaless-attributes](https://github.com/spatie/laravel-schemaless-attributes)
- [Symfony Workflow component (state-machine, not trigger/action)](https://symfony.com/doc/current/components/workflow.html)
- [openai-php/laravel](https://github.com/openai-php/laravel)
- [Laravel Excel commercial issues with Laravel 11](https://github.com/SpartnerNL/Laravel-Excel/issues/4268)

Confidence: **HIGH** for version pins (verified on Packagist 2026-04), **MEDIUM** for the build-vs-buy workflow recommendation (judgment call — Symfony Workflow is the standard but is the wrong abstraction for trigger/action; multiple sources agree on that distinction).
