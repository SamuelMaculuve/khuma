# Architecture Research — Khuma v1.0 Complete CRM

**Domain:** Brownfield multi-tenant CRM SaaS (Laravel 11 + Livewire 3) — adding Deals, Activities, Custom Fields, Workflow, Reporting, Public API, Audit Log
**Researched:** 2026-04-28
**Confidence:** HIGH (verified against codebase map; integration points cross-referenced to file paths)

---

## Integration Posture

**Rule of engagement:** Integrate WITH existing architecture. No SPA pivot. No service split. New code follows established conventions:
- New tenant-scoped tables carry `company_id` FK to `companies`
- New full-page UIs are Livewire components (`#[Layout('layouts.app')]`) registered in `routes/web.php`
- New async work = `App\Jobs\*` `ShouldQueue` classes; recurring work registered in `routes/console.php`
- New external integrations live behind `app/Services/<Name>Service.php`
- New API endpoints in `routes/api.php` under `auth:sanctum`, versioned

**New singular models** (do not perpetuate the `Leads`/`Clients` plural mistake — see CONCERNS.md). New entities ship as `Deal`, `Activity`, `CustomField`, `Workflow`, `AuditLog`, etc.

---

## 1. Multi-Tenancy Hardening — MUST PRECEDE EVERYTHING

### Recommendation: Harden BEFORE adding any new tenant-scoped table.

CONCERNS.md flags manual `where('company_id', ...)` in 8+ Livewire components and controllers. Every new module (Deals, Activities, Workflows, AuditLog, CustomFields, Reports) is tenant-scoped. Adding 5 modules atop a leaky scoping model multiplies the leak surface by 5x. Hardening is a one-time investment that pays back across every subsequent phase.

### Approach: `BelongsToCompany` trait + `SetTenantContext` middleware + audit script

**A. Tenant context middleware** — `app/Http/Middleware/SetTenantContext.php` (NEW)
- Resolves `auth()->user()->company_id` once per request
- Binds it to a singleton: `app()->instance('tenant.company_id', $companyId)`
- Registered in `bootstrap/app.php` `withMiddleware(...)` (currently empty per CONCERNS.md) under the `web` and `auth:sanctum` groups

**B. `BelongsToCompany` trait** — `app/Models/Concerns/BelongsToCompany.php` (NEW)
```php
trait BelongsToCompany {
    protected static function bootBelongsToCompany(): void {
        static::addGlobalScope('company', fn($q) =>
            app()->bound('tenant.company_id')
                ? $q->where($q->getModel()->getTable().'.company_id', app('tenant.company_id'))
                : $q
        );
        static::creating(function ($model) {
            if (empty($model->company_id) && app()->bound('tenant.company_id')) {
                $model->company_id = app('tenant.company_id');
            }
        });
    }
    public function company() { return $this->belongsTo(Companies::class, 'company_id'); }
}
```

**C. Apply to existing models (modified):**
- `app/Models/Leads.php`, `Clients.php`, `Messages.php`, `Notes.php`, `LeadHistories.php`, `EmailCampaign.php`, `EmailCampaignLog.php`, `Instance.php`, `InboundEmail.php`, `Team.php`

**D. Strip the now-redundant manual filters** in:
- `app/Livewire/EmailCampaigns.php` (8 sites)
- `app/Livewire/KanbanBoard.php:126,160,301`
- `app/Livewire/ClientList.php:56,70,83,100`
- `app/Http/Controllers/Admin/DashboardController.php:18-65`
- Replace with `Lead::all()` style; the global scope adds the filter.

**E. Bypass mechanism** for legitimate cross-tenant work (admin reports, Mailcow polling jobs, webhooks):
- `Model::withoutGlobalScope('company')` — used in `DispatchTenantInboundFetches` and the new public webhook intake jobs
- Document the *only* allowed bypass sites; lint-grep for them in CI

**F. Job context bridging** — jobs do not have an HTTP request, so `auth()` is null. Two patterns:
- Pass `$companyId` into the job constructor and re-bind: `app()->instance('tenant.company_id', $this->companyId)` in `handle()`
- Or: jobs always use `withoutGlobalScope('company')` and explicitly filter

**G. Tenant isolation regression test** — `tests/Feature/TenantIsolationTest.php` (NEW): create two companies, assert User A cannot see User B's `Lead`/`Deal`/`Activity`/`Workflow`/`AuditLog`. Run in CI.

### Output of this phase
Schema is unchanged; behavior is hardened. All subsequent phases ride on enforced isolation. Add `BelongsToCompany` to every new model from this point on (no exceptions).

---

## 2. Deals/Opportunities Module

### Domain placement: First-class entity, NOT a renamed Lead

A Deal is a sales-pipeline-stage transaction with monetary value, expected close, and probability. A Lead is a captured contact intent. Conflating them (today's situation) prevents forecasting. Industry-standard model:

```
Lead (captured intent) --[convert]--> Deal (priced opportunity) --[close won]--> Customer
                          \--> Client/Contact (person/company)
```

### Model design

**New tables** (all carry `company_id`, use `BelongsToCompany`):

| Table | Columns (key) | Purpose |
|-------|---------------|---------|
| `deals` | `id, company_id, name, client_id (nullable), lead_id (nullable, source), pipeline_id, stage_id, value_cents, currency, probability, expected_close_at, owner_id (FK users), status (open/won/lost), lost_reason, won_at, lost_at, created_by, timestamps, soft_deletes` | Opportunity record |
| `pipelines` | `id, company_id, name, is_default` | Multi-pipeline support (sales, renewal, partner) |
| `deal_stages` | `id, pipeline_id, name, position, probability_default, is_won, is_lost` | Configurable stages per pipeline |
| `deal_stage_history` | `id, deal_id, from_stage_id (nullable), to_stage_id, changed_by, changed_at, duration_seconds (nullable, time in previous stage)` | Append-only audit; powers velocity/funnel reporting |
| `deal_products` | (only if Products is in scope; v1.0 secondary) | line items |

### Lead → Deal conversion

**Pattern:** explicit conversion action (button on Lead detail), not implicit.
- New action `App\Actions\ConvertLeadToDeal` — copies relevant fields, links `deal.lead_id`, optionally sets `lead.status = 'converted'` and soft-deletes the Lead
- Triggered from `App\Livewire\LeadsManager` and `App\Livewire\KanbanBoard` (modified)
- A Lead can spawn multiple Deals over time (renewals); the FK is one-way `deal.lead_id` not `lead.deal_id`

### Stage history table — design rationale

**Append-only, not a column on `deals`.** Critical for:
- Funnel velocity reports (avg days in stage)
- "Stuck deals" alerts (in stage > N days)
- Win/loss path analysis
- Forecast drift over time

Populated by a `DealObserver` (NEW, `app/Observers/DealObserver.php`) on `updating` when `stage_id` changes. Mirrors the existing `LeadObserver`/`LeadHistories` pattern — same convention, less special-casing for future contributors.

### UI components (NEW)
- `App\Livewire\Deals\DealsKanban` — pipeline board (clone of KanbanBoard, parameterized by `pipeline_id`)
- `App\Livewire\Deals\DealsManager` — list/table view
- `App\Livewire\Deals\DealDetail` — single deal page (stage history timeline, activities, notes)
- `App\Livewire\Settings\PipelinesManager` — admin UI for pipelines/stages
- Routes in `routes/web.php` under `auth` group: `/deals`, `/deals/{deal}`, `/settings/pipelines`

### Plan-feature gating
- New `PlanFeature` keys: `deals.basic`, `deals.multi_pipeline`, `deals.forecasting`
- Existing `CheckPlanFeature` middleware applied to deal routes

---

## 3. Tasks/Activities + Calendar Sync

### Model design

**`activities` table** (NEW, `BelongsToCompany`):

| Column | Notes |
|--------|-------|
| `id, company_id` | tenant |
| `type` | enum: `call, meeting, task, email, note, whatsapp` |
| `subject, description` | content |
| `status` | enum: `pending, in_progress, done, cancelled` |
| `due_at, completed_at` | scheduling |
| `duration_minutes` | for calls/meetings |
| `assigned_to` (FK users) | owner |
| `created_by` (FK users) | author |
| `subjectable_type, subjectable_id` | **polymorphic** — Lead, Deal, Client, Ticket, Company |
| `external_event_id, external_provider, external_etag` | calendar sync state |
| `sync_status` | enum: `local, synced, dirty, conflict` |
| `reminder_at` | notification trigger |
| `timestamps, soft_deletes` | |

Polymorphic `subjectable` lets one Activity attach to a Lead OR a Deal OR a Client without forcing 3 nullable FKs. Standard Laravel `morphTo()`.

### OAuth token storage per user

**`user_calendar_accounts` table** (NEW, scoped via `user.company_id`):
| Column | Notes |
|--------|-------|
| `id, user_id` | one row per (user, provider) |
| `provider` | enum: `google, outlook` |
| `account_email` | display |
| `access_token, refresh_token` | **encrypted** (`$casts = ['access_token' => 'encrypted', 'refresh_token' => 'encrypted']`) |
| `expires_at` | refresh trigger |
| `calendar_id` | which calendar to sync |
| `sync_token / delta_link` | incremental sync cursor |
| `last_synced_at, sync_state` | health |

**Library choice:** `google/apiclient` for Google, `microsoft/microsoft-graph` for Outlook. Wrap both behind `App\Services\Calendar\CalendarProviderInterface` (NEW). This is the `MailProvisioner` interface pattern that CONCERNS.md already recommends.

### Sync queue strategy

**Outbound (Khuma → Calendar):** `PushActivityToCalendarJob` dispatched from `ActivityObserver` on `created`/`updated` (when `due_at` or `subject` changes). Idempotent via `external_event_id`.

**Inbound (Calendar → Khuma):** Two modes:
1. **Webhook (preferred where available):** Google Calendar push notifications + MS Graph subscriptions. New webhook routes `POST /api/webhooks/calendar/google` and `/calendar/microsoft` (signed). On hit, dispatch `PullCalendarChangesJob(userId)`.
2. **Polling fallback:** Scheduled job in `routes/console.php` — `Schedule::job(new PollCalendarChangesJob)->everyFifteenMinutes()` fans out per `user_calendar_accounts` row, similar to `DispatchTenantInboundFetches` pattern.

### Conflict resolution

Conflicts arise when both sides edit the same event between syncs. Store both `local_updated_at` and `external_etag`/`external_updated_at`. Resolution policy (configurable per company in `companies.calendar_conflict_policy`):
- `last_write_wins` (default; simplest)
- `external_wins` (calendar is source of truth)
- `manual` (mark `sync_status = conflict`, surface in UI banner, user picks)

Document the policy in `PITFALLS.md`. Edge cases (deleted-on-one-side, recurring events, timezone shifts) are well-known calendar-sync hazards — flag these for a dedicated research pass during the Activities phase.

### Reminders

`activities.reminder_at` polled by `Schedule::job(new DispatchActivityRemindersJob)->everyMinute()` — selects rows where `reminder_at <= now()` AND `reminder_sent_at IS NULL`, dispatches in-app notifications + email. Mirrors the existing inbound-mail polling pattern.

### UI components (NEW)
- `App\Livewire\Activities\ActivitiesList`, `ActivityDetail`, `ActivityCalendar` (week/month grid)
- Activity widget embedded in `DealDetail`, `LeadsManager`, `ClientList` (modified)
- `App\Livewire\Settings\CalendarConnections` (OAuth connect/disconnect UI)

---

## 4. Custom Fields — JSON vs EAV

### Recommendation: **Hybrid — definition table + JSON values column**

At Khuma's scale (SMB SaaS, target tenants in 10s–1000s with hundreds–thousands of leads each, **MySQL/Postgres** in production per CONCERNS.md migration plan), full EAV (`custom_field_values` row per (entity, field)) is overkill and creates pathological JOINs for list views.

Pure JSON column without a definitions table loses validation, ordering, and type safety.

### Proposed shape

**`custom_fields` table** (NEW, `BelongsToCompany`) — definitions:
| Column | Notes |
|--------|-------|
| `id, company_id` | tenant |
| `entity_type` | enum: `lead, deal, client, activity` |
| `key` | machine name; unique per `(company_id, entity_type, key)` |
| `label` | display |
| `type` | enum: `text, textarea, number, date, datetime, select, multiselect, checkbox, url, email` |
| `options` (json) | for select types |
| `validation_rules` (json) | Laravel rule strings |
| `position, is_required, is_searchable` | UX |

**JSON column on each entity** (modified migrations adding `custom_fields json nullable` to `leads`, `deals`, `clients`, `activities`).

**Why hybrid wins at our scale:**
- One row read = entity + all its custom values. No JOIN.
- Definitions table gives us labels, types, validation, ordering.
- MySQL 5.7+/Postgres 9.4+ both support JSON column indexing on specific keys (`->>'$.priority'`) for the rare case a custom field is filter-critical — promote to a real generated column at that point.
- EAV migration path remains open if a tenant ever exceeds (rare) — no schema lock-in for callers if access goes through a `CustomFieldRepository`.

**Rendering:** `App\Livewire\Components\CustomFieldsForm` Blade component takes an `$entity` and renders inputs from the `custom_fields` table; on save, validates with the per-field rules, writes back to `entity->custom_fields` JSON. Reused on Lead, Deal, Client, Activity edit screens.

**Reporting/filtering UX:** searchable fields surface in DataTables filter UI; non-searchable fields are detail-only.

---

## 5. Workflow Automation Engine

### Trigger model: **Event-driven (Laravel Events) + scheduled poll only for time-based triggers**

Polled scheduler-only would waste cycles checking "did anything happen?" on quiet tenants. Pure event-driven misses time-based triggers ("3 days after deal stage change"). Combine:

- **Synchronous triggers** dispatch on Eloquent model events (`created`, `updated`) via `WorkflowDispatcher` listener registered in `EventServiceProvider`
- **Time-based triggers** via a `Schedule::job(new EvaluateScheduledWorkflowsJob)->everyFiveMinutes()` that scans `workflow_runs` with `scheduled_at <= now()`

### Data model

**`workflows`** (NEW, `BelongsToCompany`):
| Column | Notes |
|--------|-------|
| `id, company_id, name, description` | |
| `entity_type` | what it watches: `lead, deal, activity, client` |
| `trigger_event` | `created, updated, stage_changed, status_changed, time_elapsed, webhook` |
| `trigger_config` (json) | event filter (e.g. `{"from_stage":"new","to_stage":"qualified"}`) |
| `is_active` | toggle |
| `created_by, timestamps` | |

**`workflow_conditions`** (NEW): tree of conditions linked to workflow — `(workflow_id, parent_id, type [all/any/leaf], field, operator, value)`. Or, simpler v1: store as JSON tree on `workflows.conditions` and parse with a `ConditionEvaluator` service.

**`workflow_actions`** (NEW): ordered list per workflow — `(workflow_id, position, action_type, action_config json)`. Action types:
- `send_email` (uses TenantMailer — already exists)
- `send_whatsapp` (uses Instance — already exists)
- `create_activity`
- `update_field` (set `lead.status = 'X'`)
- `assign_user`
- `webhook` (HTTP POST out)
- `wait` (sleep N minutes/hours/days — implemented via re-dispatching delayed job)
- `branch_if` (sub-condition gate)

**`workflow_runs`** (NEW): execution log — `(id, workflow_id, entity_type, entity_id, status [running/completed/failed/waiting], current_action_index, scheduled_at, started_at, completed_at, error, payload json)`. Append-only; powers debugging.

### Execution

- Trigger fires → `RunWorkflowJob(workflow, entity)` queued
- Job evaluates conditions → if pass, walks actions sequentially
- `wait` action re-queues `RunWorkflowJob` with `delay()`; persists run state in `workflow_runs`
- Failures captured in `workflow_runs.error`; user-visible run history page

### User-facing builder

Three options, in increasing complexity:

| Option | Effort | UX |
|--------|--------|----|
| **(A) Form-based linear builder** | Low | Trigger dropdown → conditions table → actions list. Like Trello/HubSpot basic rules. |
| **(B) Visual node-based** (Drawflow.js, React Flow) | High | Drag/drop graph. Out of scope for v1.0. |
| **(C) Templates + form builder hybrid** | Medium | Pre-built templates ("On lead created → send welcome email after 1 hour") that user can clone and parameterize. |

**Recommendation: Ship (A) in v1.0; add templates from (C) for adoption; (B) deferred to post-v1.**

UI: `App\Livewire\Workflows\WorkflowsList`, `WorkflowEditor`, `WorkflowRunHistory`.

### Integration with existing observers

`LeadObserver` already exists; rather than duplicate, register the workflow dispatcher as a **separate** listener on the same Eloquent events. The observer keeps writing `LeadHistories` (audit), the dispatcher keeps firing workflows. Single responsibility.

---

## 6. Reporting & Analytics

### Recommendation: **Hybrid — pre-aggregate hot rollups, live-query the rest**

| Report | Strategy | Why |
|--------|----------|-----|
| Sales funnel (deal counts/value per stage, current state) | **Live query** | Already filtered by current company via global scope; cheap with proper indexes on `(company_id, stage_id)` |
| Win/loss rate (current month, quarter, YTD) | **Live query, cached 5–15 min** | Aggregates over closed deals; cache key `(company_id, period)` |
| Conversion rate, source ROI, rep leaderboard | **Pre-aggregated nightly** into `report_snapshots` | Joins across leads + deals + activities; expensive |
| Sales velocity (avg days per stage from `deal_stage_history`) | **Pre-aggregated nightly** | Time-series, expensive aggregation |
| MRR/ARR/churn (SaaS billing side, internal-admin) | **Pre-aggregated nightly** | Cross-tenant, immutable historical periods |
| Live dashboard (today's activity, my open deals) | **Live query** | Always current |

### Pre-aggregation infrastructure

**`report_snapshots` table** (NEW, `BelongsToCompany` except for internal-admin SaaS metrics which go in `system_metrics`):
- `(id, company_id, report_type, period_start, period_end, dimension, value_numeric, value_json, computed_at)`
- Unique `(company_id, report_type, period_start, dimension)` for idempotent re-runs

**Job:** `Schedule::job(new ComputeDailyReportsJob)->dailyAt('02:00')` — fans out one `ComputeReportSnapshotJob` per company per report_type. Re-runnable for backfill.

### Where dashboards live

Following the existing convention (full-page Livewire under `routes/web.php`):
- `App\Livewire\Reports\SalesDashboard` — funnel, conversion, leaderboard for the company
- `App\Livewire\Reports\ActivityDashboard` — call/meeting/email volumes
- `App\Livewire\Reports\Forecasting` — pipeline-weighted forecast (depends on Deals)
- `App\Livewire\Admin\SaasMetrics` — internal-admin MRR/ARR/churn (replaces/extends current `Admin\DashboardController`)

Charts: **Chart.js via Livewire `wire:ignore`** (server emits JSON, JS renders). Avoid heavy SPA chart libs. ApexCharts is a viable alternative.

### Performance prerequisites

Reporting depends on:
- Custom fields schema settled (so reports can include them)
- Deal stage history populated (so velocity can be computed)
- Activities populated (so activity reports have data)

**This is why Reporting comes LAST in the build order.**

---

## 7. Public API

### Versioning

`routes/api.php` reorganized:
```
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function() {
    Route::apiResource('leads', Api\V1\LeadsController::class);
    Route::apiResource('deals', Api\V1\DealsController::class);
    Route::apiResource('clients', Api\V1\ClientsController::class);
    Route::apiResource('activities', Api\V1\ActivitiesController::class);
});
Route::prefix('webhooks')->middleware('throttle:webhooks')->group(function() {
    // Existing /save-message moved here, signature-validated
    Route::post('whatsapp/inbound', [Webhooks\WhatsAppController::class, 'store'])
        ->middleware('verify.webhook.signature:whatsapp');
    Route::post('calendar/google', ...);
    Route::post('mpesa/callback', ...);
});
```

Existing routes (`POST /api/save-message`, `POST /api/call-logs/{id}`, `GET /api/index-all`) become `/api/webhooks/...` with HMAC signature verification (closes the open issue in CONCERNS.md). The legacy paths get a one-release deprecation alias.

### Sanctum tokens per company (and per user)

Sanctum supports `personal_access_tokens` natively. Two token classes:

| Token type | Scope | Use |
|------------|-------|-----|
| **User token** | Tied to a specific user; inherits user's `company_id` and Spatie roles | Mobile app, user-driven scripts |
| **Company API key** | Tied to a `Company` directly; service account, configurable abilities | Integrations, n8n flows, partner systems |

Implement company-scoped tokens by adding `Companies` to Sanctum's tokenable types (`HasApiTokens` trait on `Companies`). Tokens render in `App\Livewire\Settings\ApiTokens` under the tenant's settings panel. Display once on creation, hash thereafter.

Token abilities (Sanctum `->createToken('name', ['leads:read', 'deals:write'])`) gate per-resource access — surface as checkboxes in the UI.

### Rate limiting

Use Laravel's `RateLimiter` facade in `App\Providers\AppServiceProvider::boot()`:
- `api`: 60/min per token (or per IP if unauthed)
- `webhooks`: 600/min per IP (high — webhooks burst)
- Public form-capture endpoints (future): 30/min per IP, with reCAPTCHA

Per-plan-feature rate limit overrides — premium tenants get higher quotas. Read `auth()->user()->company->subscription->plan->getFeature('api.rate_limit')`.

### API documentation

Auto-generate OpenAPI from controllers with `dedoc/scramble` (Laravel-native) — no separate annotation maintenance. Mount at `/api/docs`.

---

## 8. Audit Log

### Recommendation: **`spatie/laravel-activitylog`, system-wide; integrate `LeadObserver` into it**

The library already does what `LeadObserver` does manually, plus tenant scoping, plus user attribution, plus property diffs. Migrating is a net simplification, not a parallel system.

### Approach

1. Install `spatie/laravel-activitylog`. Publish migration; the `activity_log` table gains a `company_id` column and the `BelongsToCompany` trait.
2. Add `LogsActivity` trait to: `Lead`, `Deal`, `Client`, `Activity`, `Workflow`, `User`, `Subscription`, `Invoice`, `Companies` (settings changes), `CustomField` (definition changes).
3. Configure each model's `getActivitylogOptions()` to log specific attributes (avoid noise — don't log `updated_at`).
4. **Migrate `LeadObserver`:** the lifecycle audit becomes activitylog rows. The existing `LeadHistories` table either:
    - **(a) Stays for backward compat** (UI reads it as the lead-specific timeline) — lowest risk; double-write briefly during migration.
    - **(b) Gets replaced** — read `activity_log` filtered to `subject_type=Lead` for the timeline view.
   Recommendation: (a) for v1.0 to avoid touching working UI; (b) as a follow-up cleanup.
5. UI surface: `App\Livewire\Audit\AuditLog` — filterable timeline (user, entity type, date range). Per-tenant by global scope.
6. Login/role-change events — emit explicit activity entries from `AuthenticatedSessionController` and Spatie role assignment hooks (Spatie fires events; subscribe in a listener).

### Retention

`activity_log` grows fast. Add `Schedule::command('activitylog:clean')->daily()` with `ACTIVITY_LOGGER_DELETE_RECORDS_OLDER_THAN_DAYS=365` (or per-plan).

---

## 9. Build Order & Dependencies

### Dependency graph

```
[0. Multi-Tenancy Hardening] ── must precede everything (5 new modules each scoped)
        │
        ├──► [1. Audit Log]                  (cross-cutting; install early so subsequent
        │                                     phases auto-log via spatie/laravel-activitylog)
        │
        ├──► [2. Deals/Opportunities]        (foundational entity)
        │       │
        │       ├──► [3. Activities + Calendar Sync]   (Activities polymorphic-attach to Deals & Leads;
        │       │                                       must exist before Workflow can "create activity")
        │       │
        │       ├──► [4. Custom Fields]      (extend Lead, Deal, Client, Activity uniformly;
        │       │                              Activity & Deal must exist first)
        │       │
        │       ├──► [5. Workflow Automation]   (needs Activities for "create_activity" action;
        │       │                                 needs Deals for "stage_changed" trigger)
        │       │
        │       └──► [6. Reporting & Analytics] (depends on Deals, stage history, Activities,
        │                                        Custom Fields all populated)
        │
        └──► [7. Public API]                 (can ship in parallel with later phases;
                                              schema must be stable so v1 contract holds —
                                              ship after Deals + Activities + CustomFields stabilize)
```

### Recommended phase sequence

| # | Phase | Depends on | Why this position |
|---|-------|------------|-------------------|
| **0** | **Multi-tenancy hardening + mass-assignment cleanup** | — | Every subsequent module is tenant-scoped; security debt compounds otherwise. CONCERNS.md flags both. |
| **1** | **Audit log infrastructure** (`spatie/laravel-activitylog` install + tenant-aware migration + `LogsActivity` on existing models) | Phase 0 | Install before new modules so they log from day one. Cheap to retrofit later but loses history. |
| **2** | **Deals/Opportunities** (model, pipelines, stages, conversion, kanban UI, stage history) | Phases 0, 1 | First-class entity that unlocks forecasting, workflow triggers, and activity attachment. PROJECT.md priority #1. |
| **3** | **Tasks/Activities + Calendar sync** | Phase 2 (polymorphic to Deal) | Workflow needs `create_activity` action; reporting needs activity volume. Calendar sync is the longest tail — do not block on it; ship Activities standalone first, then layer sync. |
| **4** | **Custom fields** | Phase 2, 3 (must extend Deal & Activity from the start) | Late-add custom fields breaks reporting schemas. Ship before Reporting. |
| **5** | **Workflow automation** | Phases 2, 3, 4 (triggers on stage_changed; actions create activities; conditions check custom fields) | Highest dependency depth — must come after entities and fields are stable. |
| **6** | **Public API (v1) + Sanctum company tokens + webhook-signature retrofit** | Phases 2, 3, 4 (schema must be stable to lock v1 contract) | Can begin in parallel with Phase 5 but ship after — exposing an unstable schema externally creates compatibility burden. |
| **7** | **Reporting & analytics** (live dashboards + nightly snapshots) | Phases 2–4 (data populated), Phase 1 (audit-driven activity reports) | Depends on actual data flowing; pre-aggregated reports need historical depth (so a partial sprint of "data accumulation" before this phase is fine). |

### Parallelization opportunities

- Phase 1 (audit) and Phase 0 cleanup can share a single phase if the team has capacity (both are "platform hygiene")
- Phase 6 (Public API) can start once Phase 4 finishes; runs in parallel with Phase 5 if team is split
- Phase 3 calendar sync is internally splittable: Activities CRUD ship first; Google sync next sprint; Outlook sync sprint after

---

## Integration Points Summary

### NEW files (high-level inventory)

```
app/
├── Http/Middleware/SetTenantContext.php            (Phase 0)
├── Http/Middleware/VerifyWebhookSignature.php      (Phase 0/6)
├── Models/Concerns/BelongsToCompany.php            (Phase 0)
├── Models/Deal.php, Pipeline.php, DealStage.php,
│         DealStageHistory.php                       (Phase 2)
├── Models/Activity.php, UserCalendarAccount.php    (Phase 3)
├── Models/CustomField.php                          (Phase 4)
├── Models/Workflow.php, WorkflowAction.php,
│         WorkflowRun.php                            (Phase 5)
├── Models/ReportSnapshot.php                       (Phase 7)
├── Observers/DealObserver.php                      (Phase 2)
├── Observers/ActivityObserver.php                  (Phase 3)
├── Services/Calendar/CalendarProviderInterface.php (Phase 3)
├── Services/Calendar/GoogleCalendarProvider.php    (Phase 3)
├── Services/Calendar/OutlookCalendarProvider.php   (Phase 3)
├── Services/Workflow/ConditionEvaluator.php        (Phase 5)
├── Services/Workflow/ActionExecutor.php            (Phase 5)
├── Actions/ConvertLeadToDeal.php                   (Phase 2)
├── Jobs/PushActivityToCalendarJob.php              (Phase 3)
├── Jobs/PollCalendarChangesJob.php                 (Phase 3)
├── Jobs/DispatchActivityRemindersJob.php           (Phase 3)
├── Jobs/RunWorkflowJob.php                         (Phase 5)
├── Jobs/EvaluateScheduledWorkflowsJob.php          (Phase 5)
├── Jobs/ComputeReportSnapshotJob.php               (Phase 7)
├── Livewire/Deals/{DealsKanban, DealsManager,
│            DealDetail}.php                         (Phase 2)
├── Livewire/Activities/{ActivitiesList,
│            ActivityDetail, ActivityCalendar}.php   (Phase 3)
├── Livewire/Workflows/{WorkflowsList,
│            WorkflowEditor, WorkflowRunHistory}.php (Phase 5)
├── Livewire/Reports/{SalesDashboard,
│            ActivityDashboard, Forecasting}.php     (Phase 7)
├── Livewire/Settings/{PipelinesManager,
│            CalendarConnections, ApiTokens,
│            CustomFieldsManager}.php                (multi-phase)
├── Livewire/Audit/AuditLog.php                     (Phase 1)
└── Http/Controllers/Api/V1/*Controller.php          (Phase 6)
```

### MODIFIED files (key sites)

| File | Phase | Change |
|------|-------|--------|
| `bootstrap/app.php` | 0 | Register `SetTenantContext` + security headers middleware |
| `app/Models/Leads.php`, `Clients.php`, `Messages.php`, `Notes.php`, `LeadHistories.php`, `EmailCampaign.php`, `EmailCampaignLog.php`, `Instance.php`, `InboundEmail.php`, `Team.php` | 0 | Add `BelongsToCompany` trait; replace `$guarded=[]` with `$fillable=[...]`; add `LogsActivity` (Phase 1) |
| `app/Livewire/EmailCampaigns.php`, `KanbanBoard.php`, `ClientList.php` | 0 | Strip manual `where('company_id', ...)` filters |
| `app/Http/Controllers/Admin/DashboardController.php` | 0 / 7 | Strip manual filters; later replaced by Livewire `Reports\*` components |
| `app/Livewire/LeadsManager.php`, `KanbanBoard.php` | 2 | Add "Convert to Deal" action button |
| `app/Observers/LeadObserver.php` | 1 | Either remove (replaced by activitylog) or keep & double-write |
| `routes/api.php` | 6 | Move existing endpoints under `/api/webhooks/*` with signature middleware; add `/api/v1/*` resource routes |
| `routes/web.php` | each | Register new Livewire pages |
| `routes/console.php` | 3, 5, 7 | Schedule new polling jobs |
| `app/Providers/AppServiceProvider.php` | 6 | Define `RateLimiter` profiles |
| `app/Providers/EventServiceProvider.php` | 5 | Wire workflow dispatcher listeners |
| `database/migrations/` | 0 | Migration to denormalize `messages.company_id` (closes the `whereHas` perf issue and the missing direct scope) |
| `app/Models/Messages.php` | 0 | Split `sender_id` into `sender_id` (User) + `instance_id` (Instance) — per CONCERNS.md fragile-area note |

---

## Anti-Patterns to Avoid

### 1. Adding new tenant-scoped tables before global scope is in place
Compounds leak surface. Phase 0 is non-negotiable.

### 2. Treating Deal as "Lead with extra fields"
Conflation prevents forecasting and re-prospecting. Keep them separate; explicit conversion.

### 3. Putting workflow logic in Eloquent observers directly
Mixes audit (LeadHistories) with automation. Use a dedicated event listener; keep observers single-purpose.

### 4. EAV custom fields at our scale
JOIN explosion on list views. JSON column + definitions table is the right level of abstraction.

### 5. Skipping the `CalendarProviderInterface` abstraction
Hardcoding Google's SDK across the codebase blocks Outlook and forces rewrites when Google's API churns. Same lesson `MailcowService` is teaching us in retrospect.

### 6. Live-querying every report
Velocity, conversion, and rollups across joins crush the DB on dashboards. Pre-aggregate the heavy ones nightly.

### 7. Shipping a Public API v1 before Custom Fields stabilize
Custom-field-shaped responses become a breaking change. Wait until Phase 4 is done.

### 8. Letting `$guarded = []` survive into v1.0
Privilege escalation vector flagged in CONCERNS.md. Fix in Phase 0 alongside the global scope.

---

## Sources

- Existing codebase analysis: `.planning/codebase/ARCHITECTURE.md`, `STRUCTURE.md`, `CONCERNS.md` (HIGH confidence — direct file inspection)
- `.planning/PROJECT.md` (HIGH — project source of truth)
- Laravel 11 docs — Eloquent global scopes, Sanctum token abilities, Rate Limiting (HIGH — official)
- `spatie/laravel-activitylog` package documentation (HIGH — official)
- `spatie/laravel-permission` (already in use — HIGH)
- Google Calendar API push notifications, Microsoft Graph subscriptions (MEDIUM — pattern well-established but needs phase-specific re-research before Phase 3 build)
- `dedoc/scramble` for OpenAPI generation (MEDIUM — Laravel-native, growing adoption)
- General CRM domain modeling (HubSpot, Pipedrive, Close.io public schemas/docs) — Lead↔Deal separation, stage history, polymorphic activities (MEDIUM — pattern convergence across products)

---

*Architecture research for: Khuma v1.0 Complete CRM (subsequent milestone)*
*Researched: 2026-04-28*
