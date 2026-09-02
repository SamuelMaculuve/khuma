# Technology Stack

**Analysis Date:** 2026-04-28

## Languages

**Primary:**
- PHP `^8.2` — Backend application code (`composer.json:9`)
- JavaScript (ES Modules, `"type": "module"`) — Frontend interactivity, Vite build (`package.json:3`)
- Blade — Server-rendered templating (`resources/views/`)

**Secondary:**
- CSS (via Tailwind) — Styling pipeline (`resources/css/`)

## Runtime

**Environment:**
- PHP 8.2+ runtime (CLI + web)
- Node.js (Vite-driven asset pipeline; version not pinned via `.nvmrc`)
- `php artisan serve` used for local dev (`composer.json:57`)

**Package Manager:**
- Composer (PHP) — `composer.json` + `composer.lock` present
- npm (JS) — `package.json` + `package-lock.json` present

## Frameworks

**Core (Backend):**
- Laravel Framework `^11.31` — Application framework (`composer.json:11`)
- Livewire `^3.6` — Reactive server-driven UI components (`composer.json:13`); used as full-page routed components (e.g. `routes/web.php:36-46`)
- Laravel Breeze `^2.3` (dev) — Authentication scaffolding (`composer.json:19`)
- Laravel Tinker `^2.9` — REPL (`composer.json:12`)

**Authorization:**
- Spatie Laravel Permission `^6.21` — Role/permission management (`composer.json:14`, `config/permission.php`)

**Tables / Data:**
- Yajra Laravel Datatables Oracle `^11.0` — Server-side DataTables (`composer.json:15`, `config/datatables.php`)

**HTTP Client:**
- GuzzleHTTP `^7.10` — Underlying HTTP client (used via `Illuminate\Support\Facades\Http`) (`composer.json:10`)

**Frontend:**
- Tailwind CSS `^3.1.0` — Utility-first CSS (`package.json:16`, `tailwind.config.js`)
- `@tailwindcss/forms` `^0.5.2` — Form styling plugin (`package.json:9`)
- Alpine.js `^3.4.2` — Lightweight reactivity (`package.json:10`)
- Axios `^1.7.4` — HTTP client (`package.json:12`)
- Vite `^6.0.11` + `laravel-vite-plugin` `^1.2.0` — Build tooling (`package.json:14,17`, `vite.config.js`)
- PostCSS `^8.4.31` + Autoprefixer `^10.4.2` (`package.json:11,15`, `postcss.config.js`)

**Testing:**
- PHPUnit `^11.0.1` (dev) — Test framework (`composer.json:25`, `phpunit.xml`)
- Mockery `^1.6` (dev) — Mocking (`composer.json:23`)
- FakerPHP `^1.23` (dev) — Test data (`composer.json:18`)

**Dev Tools:**
- Laravel Pint `^1.13` — Code style/formatter (`composer.json:21`)
- Laravel Pail `^1.1` — Real-time log tailing (`composer.json:20`)
- Laravel Sail `^1.26` — Docker dev env (`composer.json:22`)
- Nuno Maduro Collision `^8.1` — CLI error reporting (`composer.json:24`)
- Concurrently `^9.0.1` (npm) — Run multiple dev processes (`package.json:13`)

## Database

**Default:** SQLite (`DB_CONNECTION=sqlite`, `.env.example:26`)
- Migrations under `database/migrations/`
- Factories under `database/factories/`, seeders under `database/seeders/`
- MySQL/MariaDB supported (commented config block, `.env.example:27-31`)

**ORM:**
- Eloquent (Laravel built-in) — All models in `app/Models/`

## Queue / Cache / Session

**Queue:**
- Driver: `database` (`.env.example:41`)
- Jobs: `app/Jobs/` (`SendCampaignEmails`, `FetchTenantInboundMail`, `DispatchTenantInboundFetches`, `ProvisionTenantMailDomain`, `SendLeadEmail`)
- Queue listener launched in `composer.json:57` dev script

**Cache:**
- Driver: `database` (`.env.example:43`); Redis client (`phpredis`) configured but optional (`.env.example:48-51`)

**Session:**
- Driver: `database` (`.env.example:33`), 120 min lifetime

**Broadcast:**
- `log` driver (`.env.example:39`) — no real-time broadcasting wired

**Filesystem:**
- `local` disk default (`.env.example:40`); AWS S3 envs scaffolded (`.env.example:78-82`)

## Mail

- Default mailer: `log` (`.env.example:53`)
- Mailables: `app/Mail/CampaignEmail.php`, `app/Mail/LeadDirectEmail.php`
- Per-tenant SMTP via Mailcow (see `INTEGRATIONS.md`)

## Configuration Files

- `composer.json`, `composer.lock`
- `package.json`, `package-lock.json`
- `vite.config.js`, `tailwind.config.js`, `postcss.config.js`
- `phpunit.xml`
- `config/*.php` (`app, auth, cache, database, datatables, filesystems, logging, mail, permission, queue, services, session`)
- `.env.example` (present; `.env` not read for secrets)

## Platform Requirements

**Development:**
- PHP 8.2+, Composer, Node.js, npm
- `ext-imap` recommended (used by `app/Jobs/FetchTenantInboundMail.php:32`)

**Production:**
- PHP-FPM/web server, queue worker, cron for scheduling
- Optional: Redis, MySQL, S3, Mailcow, Cloudflare DNS

---

*Stack analysis: 2026-04-28*
