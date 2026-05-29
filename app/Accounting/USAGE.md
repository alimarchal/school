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
- Reports: `https://school.test/accounting/reports/general-ledger`

The main dashboard and sidebar show the Accounting link only when the logged-in user has `accounting.view`.

## API Access

Versioned API prefix:

```text
/api/v1/accounting
```

Examples:

```text
GET /api/v1/accounting/chart-of-accounts
POST /api/v1/accounting/journal-entries
POST /api/v1/accounting/journal-entries/{journalEntry}/post
POST /api/v1/accounting/journal-entries/{journalEntry}/reverse
POST /api/v1/accounting/journal-entries/{journalEntry}/void
```

API routes use the same permission names as web routes. A user without the matching permission receives `403 Forbidden`.

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
