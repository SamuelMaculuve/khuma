# Coding Conventions

**Analysis Date:** 2026-04-28

## Naming Patterns

**Models — Plural class names (non-standard for Laravel):**
- Models use **plural** class names rather than the Laravel-conventional singular form. This is unusual and worth flagging for new contributors.
- Examples: `app/Models/Leads.php` (class `Leads`), `app/Models/Clients.php` (class `Clients`), `app/Models/Companies.php`, `app/Models/Messages.php`, `app/Models/Notes.php`, `app/Models/LeadHistories.php`.
- Singular-named models also exist where the noun is naturally singular: `app/Models/User.php`, `app/Models/Plan.php`, `app/Models/Subscription.php`, `app/Models/Invoice.php`, `app/Models/Payment.php`, `app/Models/Instance.php`, `app/Models/Team.php`, `app/Models/CallLog.php`.
- Because class names are plural, models explicitly set `$table` when needed (e.g. `protected $table = 'leads';` in `app/Models/Leads.php`). Without that override Eloquent would attempt to pluralize an already-plural noun.
- All inspected models declare `protected $guarded = [];` (mass-assignment fully open) — see `app/Models/Leads.php:16`, `app/Models/Clients.php:11`. New models should match this style unless there is a security reason to opt into `$fillable`.
- `SoftDeletes` is applied where needed (e.g. `app/Models/Leads.php:13`).
- Relationship methods are typed: `public function client(): BelongsTo`, `public function messages(): HasMany`.

**Controllers:**
- HTTP controllers live in `app/Http/Controllers/` and use **plural** resource names matching the model: `LeadsController.php`, `ClientsController.php`, `CompaniesController.php`, `MessagesController.php`, `NotesController.php`, `LeadHistoriesController.php`.
- Non-resource/utility controllers use singular descriptive names: `InstanceController.php`, `CallLogController.php`, `ProfileController.php`.
- Sub-namespaced controllers under `app/Http/Controllers/Admin/` (e.g. `DashboardController`, `UserController`), `app/Http/Controllers/API/` (e.g. `MessagesController`), and `app/Http/Controllers/Auth/`.

**Livewire components:**
- Located in `app/Livewire/` with **PascalCase** class/file names that describe a UI feature, not a model: `KanbanBoard.php`, `LeadsManager.php`, `ClientList.php`, `EmailCampaigns.php`, `WhatsAppConnection.php`, `WhatsappInterface.php`, `TicketSystem.php`, `Settings.php`, `ConnectInstance.php`, `CompanyManagement.php`, `CompanyManageInteralUsers.php` (note typo: "Interal" should be "Internal" — fix when touched).
- Sub-namespaces are used to group related flows: `app/Livewire/Admin/`, `app/Livewire/Subscription/` (`ChoosePlan`, `Checkout`, `Confirm`).

**Migrations:**
- Standard Laravel timestamp-prefixed filenames in `database/migrations/`.
- Two patterns coexist:
  1. **Create-table migrations** named `create_<table>_table.php` (e.g. `2026_01_24_203613_create_leads_table.php`).
  2. **Incremental column-add migrations** with terse, column-only filenames — `2026_01_24_211126_company_id.php`, `2026_01_30_201628_message_to.php`, `2026_01_30_220529_client_id.php`. These do not state the target table or intent in the filename, requiring readers to open the file. Prefer the conventional `add_<column>_to_<table>_table` form for new migrations.
- `down()` methods are sometimes left empty (see `database/migrations/2026_01_24_211126_company_id.php:24`) — rollbacks are not reliably supported. Always implement the reverse when adding a column.

## Language Usage

**Mixed Portuguese / English in code:**
- The codebase is primarily English, but Portuguese leaks into UI-facing code, comments, and string literals — especially in `app/Livewire/KanbanBoard.php`:
  - Inline comments: `// 'kanban' ou 'list'` (line 25), `// Propriedades para ordenação` (line 30).
  - Filter sentinel values: `public $filterStatus = 'todos';`, `public $filterPriority = 'todos';` (lines 27–28). Compare these against `'todos'` rather than `'all'`.
- Treat Portuguese as acceptable in user-facing strings/Blade output, but **write new code-level identifiers, comments, and sentinel values in English** for consistency. When extending features that already use Portuguese sentinels (`'todos'`), preserve them rather than introducing a second vocabulary.

## Livewire vs Controller Split

The application uses a hybrid routing strategy. Both styles are first-class and chosen per feature, not by layer.

**Livewire as full-page component** (route returns the component class directly):
- `routes/web.php:26` — `Route::get('/users', Users::class)`
- `routes/web.php:35` — `Route::get('/leads', LeadsManager::class)->name('leads.all')` (CRM core)
- `routes/web.php:37–40` — `/email-campaigns`, `/settings`, `/manage`
- `routes/web.php:43–44` — `/subscription/plans`, `/subscription/payment/{plan}`

**Traditional Controllers** (REST/CRUD or simple HTTP actions):
- `routes/web.php:22–23` — `DashboardController@dashboard`, `@downloadReport`
- `routes/web.php:31–32` — `Route::resource('instance', ...)`, `Route::resource('lead', ...)`
- `routes/web.php:49–51` — `ProfileController` edit/update/destroy
- `routes/web.php:54` — `Route::resource('call_logs', CallLogController::class)`

**Guideline for new code:**
- Use a **full-page Livewire component** when the screen is interactive/stateful (forms, kanban, tables with filtering).
- Use a **Controller** for plain server-rendered pages, REST resources, file downloads, or webhook-style endpoints.
- Note the inconsistency: `Route::resource('lead', LeadsController::class)` coexists with `Route::get('/leads', LeadsManager::class)`. The Livewire `LeadsManager` is the active CRM UI; the controller resource backs alternate flows.

## Blade View Organization

- Layouts: `resources/views/layouts/app.blade.php` is the primary application layout.
- Component-style layout wrapper: `resources/views/components/app-layout.blade.php` is used as `<x-app-layout>` inside Blade views.
- Livewire views mirror their components under `resources/views/livewire/` (e.g. `resources/views/livewire/kanban-board.blade.php`, `resources/views/livewire/settings.blade.php`).
- Subscription success uses a view-only route: `Route::view('/subscription/success', 'subscription.success')`.

## Route Grouping & Middleware

- Auth-protected routes are wrapped in `Route::middleware('auth')->group(...)` blocks (`routes/web.php:25`, `:48`).
- The dashboard routes apply `auth` middleware inline rather than via group (`routes/web.php:22–23`); both forms are present.
- `call_logs` resource applies middleware via `->middleware(['auth'])` after `Route::resource(...)` (`routes/web.php:54`).
- Auth scaffolding routes are extracted: `require __DIR__ . '/auth.php';` (`routes/web.php:56`).
- No prefix/name groups are used (e.g. no `->prefix('admin')->name('admin.')`); admin routes simply live under their full URL.
- **Guideline:** group all new authenticated routes inside the existing `Route::middleware('auth')->group(...)`. Add prefix/name grouping if the admin area grows.

## Code Style

**Formatter:** Laravel Pint is installed (`laravel/pint: ^1.13` in `composer.json`).
- No `pint.json` config present — Pint runs with the **default Laravel preset**.
- Run with `vendor/bin/pint` before committing.

**JS/CSS:** No `.prettierrc`, no `.eslintrc`. The frontend is Tailwind + Alpine + Vite; there is no JS lint/format pipeline.
- `package.json` exposes only `build` (`vite build`) and `dev` (`vite`) scripts.

## Imports

- Models are imported by FQCN in `use` statements at the top of each file (see `app/Models/Leads.php:6–9`, `routes/web.php:5–16`).
- Many Livewire/Controller files import a mix of `App\Models\*`, `App\Livewire\*`, and `Illuminate\*` types — group implicitly by namespace, no explicit ordering tool enforces this.

## Function & Class Design

- Models keep responsibilities to relationships and scopes; no business logic embedded.
- Livewire components hold UI state as public properties with PHP type declarations (`public bool $showLeadForm = false;`, `public ?int $lead_client_id = null;` — see `app/Livewire/KanbanBoard.php:34–36`).
- Validation lives in a `protected function rules(): array` method on the Livewire component, with conditional rules built at runtime (`KanbanBoard.php:50–70`).
- Property naming inside Livewire components mixes `camelCase` (`showLeadForm`, `viewMode`, `filterStatus`) with `snake_case` for fields that map to DB columns (`lead_client_id`, `lead_title`, `new_client_email`). Match this convention: snake_case for form fields tied to DB columns, camelCase for UI-only state.

## Error Handling & Logging

- No global exception customization observed; default Laravel handler is used.
- Logging uses the standard `Log` facade where present; no structured logging convention enforced.

---

*Convention analysis: 2026-04-28*
