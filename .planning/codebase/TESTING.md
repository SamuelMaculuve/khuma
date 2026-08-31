# Testing Patterns

**Analysis Date:** 2026-04-28

## Maturity: Minimal — Flag Honestly

**The project has effectively no automated test coverage of its CRM business logic.** Only the default Laravel scaffolding tests (Breeze auth + profile + the placeholder example) are present. None of the custom features — Leads, Clients, Companies, KanbanBoard, LeadsManager, Messages, WhatsApp integration, Email Campaigns, Subscriptions, Plans, Payments, Invoices, Teams, Instances — have any tests. There is no CI to run them automatically.

This is a significant risk area. Any refactor of CRM models, Livewire components, or subscription flows is unverified by tests. Treat new work as test-eligible: at minimum write a Feature test for new Livewire components and a Unit/Feature test for new controller endpoints.

## Test Framework

**Runner:** PHPUnit `^11.0.1` (declared in `composer.json`).
- Config: `phpunit.xml` at the project root.
- **Pest is NOT used** despite a stale `pestphp/pest-plugin` entry under `extra.allow-plugins` in `composer.json`. There are no Pest-style `test()` / `it()` calls and no Pest dependency. All test files are classic PHPUnit `extends TestCase` classes.
- Bootstrap: `vendor/autoload.php`.

**Test environment (`phpunit.xml` `<php>` block):**
- `APP_ENV=testing`
- `BCRYPT_ROUNDS=4` (fast hashing in tests)
- `CACHE_STORE=array`, `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`
- `PULSE_ENABLED=false`, `TELESCOPE_ENABLED=false`
- SQLite in-memory DB lines are **commented out**: `<!-- <env name="DB_CONNECTION" value="sqlite"/> -->`. Tests run against whatever DB the local `.env` points to. Enable the SQLite in-memory lines before running migrations-using tests in CI.

**Run command:**
```bash
vendor/bin/phpunit                       # Run all tests
vendor/bin/phpunit --testsuite=Feature   # Feature suite only
vendor/bin/phpunit --testsuite=Unit      # Unit suite only
vendor/bin/phpunit --filter=ProfileTest  # Single test class
```

No Composer script alias is defined for `test`.

## Test Suites

Defined in `phpunit.xml`:

```xml
<testsuite name="Unit">
    <directory>tests/Unit</directory>
</testsuite>
<testsuite name="Feature">
    <directory>tests/Feature</directory>
</testsuite>
```

Coverage source: `<source><include><directory>app</directory></include></source>`.

## Existing Test Files

**`tests/Unit/`** (1 file):
- `ExampleTest.php` — Default Laravel placeholder. No real assertions on app code.

**`tests/Feature/`** (2 files at root + `Auth/` subdir):
- `ExampleTest.php` — Asserts `GET /` returns 200. `RefreshDatabase` import is commented out.
- `ProfileTest.php` — Auth scaffolding test for the profile screen.

**`tests/Feature/Auth/`** (6 files — all from Laravel Breeze scaffolding):
- `AuthenticationTest.php`
- `EmailVerificationTest.php`
- `PasswordConfirmationTest.php`
- `PasswordResetTest.php`
- `PasswordUpdateTest.php`
- `RegistrationTest.php`

**Total: 9 test files, all scaffolding.** Custom CRM code is untested.

## Base Test Class

- `tests/TestCase.php` — Standard Laravel base extending `Illuminate\Foundation\Testing\TestCase`. No custom helpers, traits, or setup.

## Coverage of Custom Code

| Area | Files | Tests |
|------|-------|-------|
| Models (`Leads`, `Clients`, `Companies`, `Messages`, `Notes`, `LeadHistories`, `Instance`, `Plan*`, `Subscription*`, `Invoice`, `Payment`, `Team`, `EmailCampaign*`, `InboundEmail`, `CallLog`) | ~20 | **0** |
| Livewire (`KanbanBoard`, `LeadsManager`, `ClientList`, `EmailCampaigns`, `WhatsAppConnection`, `WhatsappInterface`, `TicketSystem`, `Settings`, `CompanyManagement`, `ConnectInstance`, Subscription/*, Admin/*) | ~14 | **0** |
| Controllers (`LeadsController`, `ClientsController`, `CompaniesController`, `MessagesController`, `NotesController`, `LeadHistoriesController`, `InstanceController`, `CallLogController`, `ProfileController`, `Admin/*`, `API/*`) | ~12 | **0** (except `ProfileController` indirectly via `ProfileTest`) |
| Migrations / Schema | 30 | **0** |
| Auth scaffolding | — | 6 (Breeze defaults) |

## Patterns to Adopt for New Tests

Because no internal patterns exist beyond Breeze, new tests should follow Laravel + PHPUnit defaults:

```php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Leads;

class LeadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_leads_screen(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/leads');
        $response->assertStatus(200);
    }
}
```

For Livewire components, use `Livewire\Livewire::test(LeadsManager::class)` (requires `livewire/livewire` already in the dependency tree).

## Factories & Fixtures

- Only `User` has a factory (`database/factories/UserFactory.php`, used by Breeze tests).
- **No factories exist for `Leads`, `Clients`, `Companies`, or any other model.** Add factories alongside any new test.

## CI / CD

**No CI is configured.**
- `.github/workflows/` directory **does not exist**. (The `.github/` directory exists but contains only `.github/appmod/`, unrelated to GitHub Actions.)
- No `.gitlab-ci.yml`, no `circleci`, no Bitbucket pipelines, no Jenkinsfile.
- Tests are not executed automatically on push or pull request.

**Recommendation:** Add a minimal `.github/workflows/ci.yml` that runs `composer install`, enables the SQLite in-memory env vars in `phpunit.xml`, and executes `vendor/bin/phpunit`. Without CI, the existing scaffolding tests are also at risk of silent breakage.

## Coverage Reports

Not configured. PHPUnit can produce coverage via Xdebug/PCOV but no `<coverage>` block or report path is set in `phpunit.xml`.

---

*Testing analysis: 2026-04-28*
