# School Accounting Module Guide

This is the single source of truth for the accounting module in `/Users/alirazamarhcal/Herd/school`. Give this file to an LLM when it needs to understand, extend, debug, or port the module.

## Application Context

- Laravel: 13.12.0
- PHP: ^8.3, local runtime PHP 8.4
- Primary database: PostgreSQL
- Frontend: Inertia Laravel 3, Inertia React 3.3, React 19.2, Tailwind 4.3
- Tests: Pest 4
- Installed packages used by this module:
  - `spatie/laravel-permission` for roles and permissions
  - `spatie/laravel-activitylog` for model activity logging
  - `spatie/laravel-query-builder` for explicit filters
  - `spatie/laravel-backup` available for future backup monitoring

## Module Location

The module is implemented as an internal package-style layer:

- `app/Accounting`
- `app/Concerns/Accounting`
- `config/accounting.php`
- `routes/accounting.php`
- `routes/accounting-api.php`
- `resources/js/pages/accounting`
- `tests/Feature/Accounting`

`App\Accounting\AccountingServiceProvider` is registered in `bootstrap/providers.php`.

## Current Implementation Status

Implemented:

- accounting service provider
- config file
- web routes
- versioned API routes
- migrations with `accounting_` table prefix
- idempotent seeders
- Spatie permissions and roles
- user-tracking concerns
- activity logging concern
- models and relationships
- PostgreSQL/MySQL/MariaDB/SQLite database object classes
- install/seed/sync/verify/health/snapshot/period commands
- journal entry create/post/reverse/void flow
- journal entry draft edit/update flow
- accounting period close/reopen flow
- balance snapshots
- general ledger report query
- trial balance report query
- balance sheet report query
- income statement report query
- full web CRUD routes for setup resources
- full API CRUD routes for setup resources
- full API CRUD routes for tax codes and tax rates
- read-only API routes for account balance snapshots
- journal entry create/show/post/reverse/void API endpoints
- journal entry draft update API endpoint
- reusable Inertia list/create/edit/show pages for setup resources
- chart of accounts create/edit/list/tree pages
- journal entry list/create/show pages
- journal entry edit page support through the shared form
- searchable dropdowns for large accounting option sets
- Spatie Query Builder filters on list screens
- focused Pest tests

## Architecture

### Provider

`App\Accounting\AccountingServiceProvider`

Responsibilities:

- merges `config/accounting.php`
- loads `routes/accounting.php`
- loads `routes/accounting-api.php`
- registers console commands
- registers `AccountingDatabaseObjectSynchronizer`

### Concerns

Location: `app/Concerns/Accounting`

- `HasAccountingValidationRules`
- `HasUserTracking`
- `LogsAccountingActivity`
- `HasAccountingPermissions`

`HasUserTracking` follows the app's existing concern style and ports the useful Moontraders behavior:

- sets `created_by` on create
- sets `updated_by` on create/update
- exposes `creator()`
- exposes `updater()`

### Base Model

`App\Accounting\Models\AccountingModel`

Uses:

- `HasUserTracking`
- Spatie `LogsActivity`
- `LogsAccountingActivity`

Most user-managed accounting models extend this base model.

## Database Tables

Tables use the `accounting_` prefix to avoid collisions.

Migration sequence:

1. `accounting_account_types`
2. `accounting_currencies`
3. `accounting_periods`
4. `accounting_chart_of_accounts`
5. `accounting_cost_centers`
6. `accounting_journal_entries`
7. `accounting_journal_entry_lines`
8. `accounting_bank_accounts`
9. `accounting_reconciliations`
10. `accounting_tax_codes`
11. `accounting_tax_rates`
12. `accounting_audit_logs`
13. `accounting_account_balance_snapshots`
14. `accounting_database_objects`

Important migration rules:

- use Laravel Schema Builder where possible
- use explicit FK/index names
- use `restrictOnDelete`, `nullOnDelete`, or `cascadeOnDelete` intentionally
- keep driver-specific SQL in database object classes
- keep migrations reversible

## Seeders

Entry point:

- `App\Accounting\Database\Seeders\AccountingDatabaseSeeder`

Seeder order:

1. `AccountingPermissionSeeder`
2. `AccountingAccountTypeSeeder`
3. `AccountingCurrencySeeder`
4. `AccountingPeriodSeeder`
5. `AccountingChartOfAccountSeeder`
6. `AccountingCostCenterSeeder`
7. `AccountingTaxCodeSeeder`
8. `AccountingTaxRateSeeder`

Seeder rules:

- idempotent
- no hardcoded numeric IDs
- stable codes only
- no destructive COA deletion
- safe to run repeatedly
- generic accounting stays separate from school-specific future accounts

## Commands

Available commands:

- `php artisan accounting:install`
- `php artisan accounting:seed`
- `php artisan accounting:sync-db-objects`
- `php artisan accounting:verify`
- `php artisan accounting:health-check`
- `php artisan accounting:rebuild-snapshots`
- `php artisan accounting:close-period {period_id}`
- `php artisan accounting:open-period {period_id}`

Install sequence:

1. run migrations
2. run accounting seeders
3. sync database objects
4. verify setup
5. print next steps

## API Design

Professional API prefix:

- `/api/v1/accounting`

Route name prefix:

- `api.accounting.*`

Current API routes:

- `apiResource /api/v1/accounting/account-types`
- `apiResource /api/v1/accounting/currencies`
- `apiResource /api/v1/accounting/periods`
- `apiResource /api/v1/accounting/chart-of-accounts`
- `apiResource /api/v1/accounting/cost-centers`
- `apiResource /api/v1/accounting/bank-accounts`
- `apiResource /api/v1/accounting/reconciliations`
- `apiResource /api/v1/accounting/tax-codes`
- `apiResource /api/v1/accounting/tax-rates`
- `GET /api/v1/accounting/account-balance-snapshots`
- `GET /api/v1/accounting/account-balance-snapshots/{snapshot}`
- `GET /api/v1/accounting/journal-entries`
- `POST /api/v1/accounting/journal-entries`
- `PUT/PATCH /api/v1/accounting/journal-entries/{journal_entry}`
- `GET /api/v1/accounting/journal-entries/{journal_entry}`
- `POST /api/v1/accounting/journal-entries/{journalEntry}/post`
- `POST /api/v1/accounting/journal-entries/{journalEntry}/reverse`
- `POST /api/v1/accounting/journal-entries/{journalEntry}/void`

Current auth middleware:

- `auth`

Recommended next step for third-party/mobile API consumers:

- run Laravel's `php artisan install:api`
- add Sanctum
- switch accounting API middleware to `auth:sanctum`

API rules:

- use versioned routes
- use plural resource names
- use `Route::apiResource` when adding full CRUD controllers
- use Form Requests for validation
- use Eloquent API Resources for output
- use explicit allowed filters through Spatie Query Builder
- never expose internal table names in public JSON keys
- use stable route names
- return paginated collections for list endpoints
- keep web/Inertia controllers separate from API controllers

API controller namespace:

- `App\Accounting\Http\Controllers\Api`

API controllers:

- `SimpleAccountingApiController`
- `AccountTypeApiController`
- `CurrencyApiController`
- `AccountingPeriodApiController`
- `ChartOfAccountApiController`
- `CostCenterApiController`
- `BankAccountApiController`
- `ReconciliationApiController`
- `JournalEntryApiController`

API resources:

- `AccountResource`
- `JournalEntryResource`

## Web Routes

Web prefix:

- `/accounting`

Route name prefix:

- `accounting.*`

Middleware:

- `web`
- `auth`
- `verified`

Current web routes include:

- dashboard
- account types
- currencies
- periods
- chart of accounts
- chart of accounts tree
- cost centers
- bank accounts
- reconciliations
- journal entries
- journal entry draft edit/update
- journal post/reverse/void actions
- tax codes
- tax rates
- account balance snapshots
- general ledger
- trial balance
- balance sheet
- income statement
- audit logs

## Permissions

Uses Spatie Permission.

Seeded roles:

- `super-admin`
- `accountant`
- `viewer`

Seeded permissions include:

- `accounting.view`
- `accounting.manage-settings`
- `account-types.view/create/update/delete`
- `currencies.view/create/update/delete`
- `periods.view/create/update/delete/close/reopen`
- `chart-of-accounts.view/create/update/delete`
- `cost-centers.view/create/update/delete`
- `journal-entries.view/create/update/delete/post/reverse/void`
- `bank-accounts.view/create/update/delete`
- `reconciliations.view/create/update/delete`
- `tax-codes.view/create/update/delete`
- `tax-rates.view/create/update/delete`
- `account-balance-snapshots.view`
- `reports.general-ledger.view`
- `reports.trial-balance.view`
- `reports.balance-sheet.view`
- `reports.income-statement.view`
- `audit-logs.view`

Every web and API accounting route is protected with explicit Laravel `can:*` middleware. Permissions are checked per action, so removing `journal-entries.post` only blocks posting, removing `chart-of-accounts.update` only blocks edit/update, and removing `accounting.view` blocks the accounting dashboard.

Navigation also respects permissions:

- `/dashboard` shows the Accounting card only when `accounting.view` is granted.
- The sidebar shows the Accounting item only when `accounting.view` is granted.
- Hidden navigation is convenience only; route middleware is the real enforcement layer.

## Accounting Rules

Core rules enforced in service/database layers:

- exactly one base currency
- FX rate must be positive
- journal entries must balance before posting
- each line must have either debit or credit
- each line cannot have both debit and credit
- each line cannot have zero debit and zero credit
- no posting to group accounts
- no posting to inactive accounts
- no posting into closed periods
- posted entries cannot be directly edited by services
- posted entries should be reversed instead of deleted
- reversal creates a new opposite posted journal entry
- period close creates snapshots
- inactive accounts with history remain reportable
- critical journal actions write audit records

## Services And Actions

Services:

- `AccountingDatabaseObjectSynchronizer`
- `AccountingHealthCheckService`
- `CurrencyConversionService`
- `JournalEntryService`

Actions:

- `PostJournalEntryAction`
- `ReverseJournalEntryAction`
- `VoidJournalEntryAction`
- `CloseAccountingPeriodAction`
- `ReopenAccountingPeriodAction`
- `CreateAccountBalanceSnapshotsAction`

Reports:

- `GeneralLedgerReport`
- `TrialBalanceReport`
- `BalanceSheetReport`
- `IncomeStatementReport`
- `CashBookReport`
- `BankBookReport`

Rule for future work:

- do not hardcode account IDs
- use stable account codes from `config/accounting.php`

## Database Driver Objects

Driver classes:

- `PostgresAccountingDatabaseObjects`
- `MySqlAccountingDatabaseObjects`
- `MariaDbAccountingDatabaseObjects`
- `SqliteAccountingDatabaseObjects`

PostgreSQL is the first production target.

SQLite is for fast tests only.

Database objects currently cover:

- single base currency unique enforcement for PostgreSQL
- check constraints for PostgreSQL
- general ledger view
- trial balance view
- balance sheet view
- income statement view
- posted-only report totals so draft journal lines do not leak into trial balance, balance sheet, or income statement

Verified database engines:

- PostgreSQL using `.env` `DB_CONNECTION=pgsql`, `DB_DATABASE=school`
- MariaDB using command-level override `DB_CONNECTION=mariadb`, `DB_DATABASE=school`
- MySQL using command-level override `DB_CONNECTION=mysql`, `DB_DATABASE=sch_mysql`

Do not permanently uncomment alternate database blocks in `.env` just to test. Prefer command-level overrides:

```bash
DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=school DB_USERNAME=root DB_PASSWORD= php artisan migrate:fresh --seed --no-interaction
DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=school DB_USERNAME=root DB_PASSWORD= php artisan accounting:install --no-interaction
DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=school DB_USERNAME=root DB_PASSWORD= php artisan accounting:verify --no-interaction

DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=sch_mysql DB_USERNAME=root DB_PASSWORD= php artisan migrate:fresh --seed --no-interaction
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=sch_mysql DB_USERNAME=root DB_PASSWORD= php artisan accounting:install --no-interaction
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=sch_mysql DB_USERNAME=root DB_PASSWORD= php artisan accounting:verify --no-interaction
```

## General Ledger

GL report class:

- `App\Accounting\Reports\GeneralLedgerReport`

Supports:

- date range
- account filter
- status filter
- order-free aggregate totals for PostgreSQL compatibility
- total debit
- total credit
- closing balance
- pagination-ready query

Future GL enhancements:

- opening balance by date
- running balance per row
- cost center filter
- currency filter
- XLSX/PDF export

## Inertia Pages

Location:

- `resources/js/pages/accounting`

Current pages:

- `dashboard.tsx`
- `simple-index.tsx`
- `resources/index.tsx`
- `resources/form.tsx`
- `resources/show.tsx`
- `chart-of-accounts/index.tsx`
- `chart-of-accounts/form.tsx`
- `chart-of-accounts/tree.tsx`
- `journal-entries/index.tsx`
- `journal-entries/form.tsx`
- `journal-entries/show.tsx`
- `reports/general-ledger.tsx`
- `reports/trial-balance.tsx`
- `reports/balance-sheet.tsx`
- `reports/income-statement.tsx`

UI rules:

- use existing app layout conventions
- use dense operational screens
- avoid landing-page style
- use existing UI components when expanding forms
- use Wayfinder route helpers when generated route files exist

## Tests

Current focused accounting tests:

- `tests/Feature/Accounting/AccountingInstallTest.php`
- `tests/Feature/Accounting/AccountingApiTest.php`
- `tests/Feature/Accounting/JournalEntryPostingTest.php`
- `tests/Feature/Accounting/AccountingPermissionAccessTest.php`

Covered:

- seed idempotency
- healthy install verification
- command registration
- balanced journal posting
- unbalanced journal rejection
- group account posting rejection
- journal reversal
- closed period posting rejection
- versioned API chart of accounts index
- versioned API chart of accounts route-model show
- versioned API journal validation errors
- permission-gated dashboard access
- permission removal blocks the next request
- create routes require create permissions
- API routes require matching resource permissions
- CRUD/report URL smoke coverage
- COA and journal filter URL smoke coverage
- draft journal edit/update flow

Verification commands:

```bash
php artisan test --compact
php artisan test --compact tests/Feature/Accounting
php artisan accounting:install
php artisan accounting:verify
vendor/bin/pint --dirty --format agent
npm run types:check
npm run lint:check
```

Last known verification:

- full Pest suite: 78 passed, 203 assertions
- accounting Pest slice: 39 passed, 67 assertions
- TypeScript check: passed
- ESLint check: passed
- Pint: passed
- Vite production build: passed
- PostgreSQL `migrate:fresh --seed`: passed
- PostgreSQL `accounting:install`: passed
- PostgreSQL `accounting:verify`: passed with 83 COA rows
- MariaDB `migrate:fresh --seed`: passed
- MariaDB `accounting:install`: passed
- MariaDB `accounting:verify`: passed with 83 COA rows
- MySQL `migrate:fresh --seed`: passed
- MySQL `accounting:install`: passed
- MySQL `accounting:verify`: passed with 83 COA rows

## Future Implementation Checklist

When extending this module, preserve this order:

1. update this file with the intended change
2. add/update migration if schema changes
3. add/update model relationship/casts/fillable
4. add/update seeder if default data changes
5. add/update service/action logic
6. add/update Form Request validation
7. add/update API resource/controller
8. add/update web/Inertia controller and page
9. add/update permissions
10. add/update Pest tests
11. run verification commands

## Important Constraints

- Do not add dependencies without approval.
- Do not hardcode numeric account IDs.
- Do not delete the COA in seeders.
- Do not mix school-specific accounts into the generic base COA.
- Keep API and web controllers separate.
- Keep database-engine-specific SQL out of normal migrations except through database object synchronizer classes.
- Keep every accounting route behind explicit `can:*` middleware.
- After changing permissions in code or seeders, clear Spatie's permission cache.

## Usage Guide

Operational usage instructions live in:

- `app/Accounting/USAGE.md`
