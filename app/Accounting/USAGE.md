# Accounting Module Usage

This guide explains how to enable, access, and permission the reusable accounting module inside the `school` Laravel app.

## Install Or Refresh

Run this after a fresh clone, a new database, or when accounting seed data/permissions need refreshing:

```bash
php artisan accounting:install
php artisan accounting:verify
```

The install command runs migrations, seeds accounting defaults, syncs database views/constraints, and verifies the module.

## Web Access

Main entry points:

- App dashboard: `https://school.test/dashboard`
- Accounting dashboard: `https://school.test/accounting`
- Chart of accounts: `https://school.test/accounting/chart-of-accounts`
- Journal entries: `https://school.test/accounting/journal-entries`
- Account types: `https://school.test/accounting/account-types`
- Currencies: `https://school.test/accounting/currencies`
- Accounting periods: `https://school.test/accounting/periods`
- Cost centers: `https://school.test/accounting/cost-centers`
- Bank accounts: `https://school.test/accounting/bank-accounts`
- Reconciliations: `https://school.test/accounting/reconciliations`
- Tax codes: `https://school.test/accounting/tax-codes`
- Tax rates: `https://school.test/accounting/tax-rates`
- Account balance snapshots: `https://school.test/accounting/account-balance-snapshots`
- Reports: `https://school.test/accounting/reports/general-ledger`
- Cash flow: `https://school.test/accounting/reports/cash-flow`
- Aged receivables: `https://school.test/accounting/reports/aged-receivables`
- Aged payables: `https://school.test/accounting/reports/aged-payables`
- Account statement: `https://school.test/accounting/reports/account-statement`

The main dashboard and sidebar show the Accounting link only when the logged-in user has `accounting.view`.

List pages use paginated queries. COA, journal entries, and reusable setup screens support explicit Spatie Query Builder filters through `filter[...]` query parameters.

```text
/accounting/chart-of-accounts?filter[account_code]=1101
/accounting/chart-of-accounts?filter[is_group]=0&filter[is_active]=1
/accounting/journal-entries?filter[status]=posted
/accounting/journal-entries?filter[reference]=RCPT&filter[entry_date_from]=2026-01-01&filter[entry_date_to]=2026-12-31
```

Journal entry create/edit screens use searchable account and cost center dropdowns. COA forms use searchable parent/type/currency dropdowns.

Exports are available for reports:

```text
/accounting/reports/general-ledger/export/csv
/accounting/reports/general-ledger/export/xlsx
/accounting/reports/general-ledger/export/pdf
/accounting/reports/trial-balance/export/xlsx
/accounting/reports/balance-sheet/export/pdf
```

## API Access

Versioned API prefix:

```text
/api/v1/accounting
```

Examples:

```text
GET /api/v1/accounting/chart-of-accounts
GET /api/v1/accounting/chart-of-accounts?filter[account_code]=1101
POST /api/v1/accounting/journal-entries
PUT /api/v1/accounting/journal-entries/{journalEntry}
POST /api/v1/accounting/journal-entries/{journalEntry}/post
POST /api/v1/accounting/journal-entries/{journalEntry}/reverse
POST /api/v1/accounting/journal-entries/{journalEntry}/void
GET /api/v1/accounting/tax-codes
GET /api/v1/accounting/tax-rates
GET /api/v1/accounting/account-balance-snapshots
```

API routes use the same permission names as web routes. A user without the matching permission receives `403 Forbidden`.

The API is now Sanctum-ready. `laravel/sanctum` is installed and `App\Models\User` uses `HasApiTokens`. Accounting API middleware is configurable:

```env
ACCOUNTING_API_MIDDLEWARE=api,auth:sanctum
```

Create an API token for an external/mobile client:

```php
$token = $user->createToken('mobile-accounting')->plainTextToken;
```

Then send:

```text
Authorization: Bearer {token}
```

## Permission Model

Permissions are seeded by:

```bash
php artisan accounting:seed
```

Roles:

- `super-admin`: all accounting permissions
- `accountant`: operational accounting permissions
- `viewer`: read-only accounting/report permissions

Permission examples:

```php
$user->givePermissionTo('accounting.view');
$user->givePermissionTo('journal-entries.create');
$user->givePermissionTo('journal-entries.post');

$user->revokePermissionTo('journal-entries.post');
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
```

After a permission is removed, the next request is blocked by route middleware.

## Route Permission Rules

Web and API routes are protected per action:

- `*.view`: index/show/read routes
- `*.create`: create/store routes
- `*.update`: edit/update routes
- `*.delete`: destroy routes
- `journal-entries.post`: journal post action
- `journal-entries.reverse`: journal reverse action
- `journal-entries.void`: journal void action
- `reports.*.view`: report pages
- `audit-logs.view`: accounting audit log page

## Service Usage

Use services/actions when accounting behavior is needed from another module. Do not write journal rows directly unless you are building a low-level import that calls the same validation rules afterward.

```php
$entry = app(\App\Accounting\Services\JournalEntryService::class)->create([
    'entry_date' => now()->toDateString(),
    'reference' => 'RCPT-001',
    'description' => 'Fee received',
    'auto_post' => true,
    'lines' => [
        ['chart_of_account_id' => $cashAccount->id, 'debit' => 1000, 'credit' => 0],
        ['chart_of_account_id' => $incomeAccount->id, 'debit' => 0, 'credit' => 1000],
    ],
]);

$entry = app(\App\Accounting\Services\JournalEntryService::class)->updateDraft($entry, $payload);

app(\App\Accounting\Services\JournalEntryService::class)->post($entry);
app(\App\Accounting\Services\JournalEntryService::class)->reverse($postedEntry, 'Correction');
app(\App\Accounting\Actions\VoidJournalEntryAction::class)->execute($draftEntry);
```

Reports:

```php
$glRows = app(\App\Accounting\Reports\GeneralLedgerReport::class)->query([
    'account_id' => $cashAccount->id,
    'date_from' => '2026-01-01',
    'date_to' => '2026-12-31',
])->paginate(50);

$totals = app(\App\Accounting\Reports\GeneralLedgerReport::class)->totals([
    'account_id' => $cashAccount->id,
]);
```

Period close/reopen:

```php
app(\App\Accounting\Actions\CloseAccountingPeriodAction::class)->execute($period);
app(\App\Accounting\Actions\ReopenAccountingPeriodAction::class)->execute($period);
```

Use stable account codes from `config/accounting.php` or `ChartOfAccount::where('account_code', ...)`; never hardcode numeric IDs.

Simple account-code journal helper:

```php
$entry = app(\App\Accounting\Services\SimpleJournalService::class)->expense(
    expenseAccountCode: '5104',
    paidFromAccountCode: '1101',
    amount: 100,
    description: 'Stationery purchase',
    reference: 'EXP-100',
    post: true,
);

$entry = app(\App\Accounting\Services\SimpleJournalService::class)->createBalancedEntry(
    debitAccountCode: '5104',
    creditAccountCode: '1101',
    amount: 100,
    description: 'Any debit/credit entry',
    reference: 'JV-100',
    post: false,
);
```

Year-end close:

```php
app(\App\Accounting\Actions\CloseFiscalYearAction::class)->execute($period);
// or
php artisan accounting:close-fiscal-year {period_id}
```

Bank reconciliation matching:

```php
$candidates = app(\App\Accounting\Services\BankReconciliationMatcher::class)->candidates($reconciliation);

app(\App\Accounting\Services\BankReconciliationMatcher::class)->reconcile(
    $reconciliation,
    $candidates->pluck('id')->all(),
);
```

Spatie permissions best practice used here:

- assign permissions to roles by seeders
- assign roles to users
- check permission names through `can:*` route middleware and `user()->can()`
- use direct user permissions only for exceptions
- after changing permissions directly, call `app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions()`

## Verification

Run these after changing routes, permissions, migrations, reports, or UI:

```bash
php artisan test --compact tests/Feature/Accounting
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run types:check
npm run lint:check
```

Database driver checks:

```bash
php artisan migrate:fresh --seed --no-interaction
php artisan accounting:install --no-interaction
php artisan accounting:verify --no-interaction

DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=school DB_USERNAME=root DB_PASSWORD= php artisan migrate:fresh --seed --no-interaction
DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=school DB_USERNAME=root DB_PASSWORD= php artisan accounting:install --no-interaction
DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=school DB_USERNAME=root DB_PASSWORD= php artisan accounting:verify --no-interaction

DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=sch_mysql DB_USERNAME=root DB_PASSWORD= php artisan migrate:fresh --seed --no-interaction
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=sch_mysql DB_USERNAME=root DB_PASSWORD= php artisan accounting:install --no-interaction
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=sch_mysql DB_USERNAME=root DB_PASSWORD= php artisan accounting:verify --no-interaction
```

Use command-level database overrides instead of permanently changing `.env`.
