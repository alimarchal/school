<?php

use App\Accounting\Http\Controllers\AccountBalanceSnapshotController;
use App\Accounting\Http\Controllers\AccountingDashboardController;
use App\Accounting\Http\Controllers\AccountingPeriodController;
use App\Accounting\Http\Controllers\AccountTypeController;
use App\Accounting\Http\Controllers\AuditLogController;
use App\Accounting\Http\Controllers\BankAccountController;
use App\Accounting\Http\Controllers\ChartOfAccountController;
use App\Accounting\Http\Controllers\CostCenterController;
use App\Accounting\Http\Controllers\CurrencyController;
use App\Accounting\Http\Controllers\JournalEntryController;
use App\Accounting\Http\Controllers\ReconciliationController;
use App\Accounting\Http\Controllers\Reports\BalanceSheetController;
use App\Accounting\Http\Controllers\Reports\GeneralLedgerController;
use App\Accounting\Http\Controllers\Reports\IncomeStatementController;
use App\Accounting\Http\Controllers\Reports\TrialBalanceController;
use App\Accounting\Http\Controllers\TaxCodeController;
use App\Accounting\Http\Controllers\TaxRateController;
use Illuminate\Support\Facades\Route;

$resourceRoutes = function (string $uri, string $controller, string $routeName, string $permissionPrefix): void {
    Route::get($uri, [$controller, 'index'])
        ->name("{$routeName}.index")
        ->middleware("can:{$permissionPrefix}.view");
    Route::get("{$uri}/create", [$controller, 'create'])
        ->name("{$routeName}.create")
        ->middleware("can:{$permissionPrefix}.create");
    Route::post($uri, [$controller, 'store'])
        ->name("{$routeName}.store")
        ->middleware("can:{$permissionPrefix}.create");
    Route::get("{$uri}/{record}", [$controller, 'show'])
        ->name("{$routeName}.show")
        ->middleware("can:{$permissionPrefix}.view");
    Route::get("{$uri}/{record}/edit", [$controller, 'edit'])
        ->name("{$routeName}.edit")
        ->middleware("can:{$permissionPrefix}.update");
    Route::match(['put', 'patch'], "{$uri}/{record}", [$controller, 'update'])
        ->name("{$routeName}.update")
        ->middleware("can:{$permissionPrefix}.update");
    Route::delete("{$uri}/{record}", [$controller, 'destroy'])
        ->name("{$routeName}.destroy")
        ->middleware("can:{$permissionPrefix}.delete");
};

Route::middleware(['web', 'auth', 'verified'])
    ->prefix(config('accounting.route_prefix', 'accounting'))
    ->name('accounting.')
    ->group(function () use ($resourceRoutes): void {
        Route::get('/', AccountingDashboardController::class)
            ->name('dashboard')
            ->middleware('can:accounting.view');

        $resourceRoutes('account-types', AccountTypeController::class, 'account-types', 'account-types');
        $resourceRoutes('currencies', CurrencyController::class, 'currencies', 'currencies');
        $resourceRoutes('periods', AccountingPeriodController::class, 'periods', 'periods');

        Route::get('chart-of-accounts/tree', [ChartOfAccountController::class, 'tree'])
            ->name('chart-of-accounts.tree')
            ->middleware('can:chart-of-accounts.view');
        Route::get('chart-of-accounts', [ChartOfAccountController::class, 'index'])
            ->name('chart-of-accounts.index')
            ->middleware('can:chart-of-accounts.view');
        Route::get('chart-of-accounts/create', [ChartOfAccountController::class, 'create'])
            ->name('chart-of-accounts.create')
            ->middleware('can:chart-of-accounts.create');
        Route::post('chart-of-accounts', [ChartOfAccountController::class, 'store'])
            ->name('chart-of-accounts.store')
            ->middleware('can:chart-of-accounts.create');
        Route::get('chart-of-accounts/{chartOfAccount}/edit', [ChartOfAccountController::class, 'edit'])
            ->name('chart-of-accounts.edit')
            ->middleware('can:chart-of-accounts.update');
        Route::match(['put', 'patch'], 'chart-of-accounts/{chartOfAccount}', [ChartOfAccountController::class, 'update'])
            ->name('chart-of-accounts.update')
            ->middleware('can:chart-of-accounts.update');
        Route::delete('chart-of-accounts/{chartOfAccount}', [ChartOfAccountController::class, 'destroy'])
            ->name('chart-of-accounts.destroy')
            ->middleware('can:chart-of-accounts.delete');

        $resourceRoutes('cost-centers', CostCenterController::class, 'cost-centers', 'cost-centers');
        $resourceRoutes('bank-accounts', BankAccountController::class, 'bank-accounts', 'bank-accounts');
        $resourceRoutes('reconciliations', ReconciliationController::class, 'reconciliations', 'reconciliations');
        $resourceRoutes('tax-codes', TaxCodeController::class, 'tax-codes', 'tax-codes');
        $resourceRoutes('tax-rates', TaxRateController::class, 'tax-rates', 'tax-rates');
        Route::get('account-balance-snapshots', [AccountBalanceSnapshotController::class, 'index'])
            ->name('account-balance-snapshots.index')
            ->middleware('can:account-balance-snapshots.view');
        Route::get('account-balance-snapshots/{record}', [AccountBalanceSnapshotController::class, 'show'])
            ->name('account-balance-snapshots.show')
            ->middleware('can:account-balance-snapshots.view');

        Route::get('journal-entries', [JournalEntryController::class, 'index'])
            ->name('journal-entries.index')
            ->middleware('can:journal-entries.view');
        Route::get('journal-entries/create', [JournalEntryController::class, 'create'])
            ->name('journal-entries.create')
            ->middleware('can:journal-entries.create');
        Route::post('journal-entries', [JournalEntryController::class, 'store'])
            ->name('journal-entries.store')
            ->middleware('can:journal-entries.create');
        Route::get('journal-entries/{journalEntry}/edit', [JournalEntryController::class, 'edit'])
            ->name('journal-entries.edit')
            ->middleware('can:journal-entries.update');
        Route::match(['put', 'patch'], 'journal-entries/{journalEntry}', [JournalEntryController::class, 'update'])
            ->name('journal-entries.update')
            ->middleware('can:journal-entries.update');
        Route::get('journal-entries/{journalEntry}', [JournalEntryController::class, 'show'])
            ->name('journal-entries.show')
            ->middleware('can:journal-entries.view');
        Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])
            ->name('journal-entries.post')
            ->middleware('can:journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])
            ->name('journal-entries.reverse')
            ->middleware('can:journal-entries.reverse');
        Route::post('journal-entries/{journalEntry}/void', [JournalEntryController::class, 'void'])
            ->name('journal-entries.void')
            ->middleware('can:journal-entries.void');

        Route::get('reports/general-ledger', GeneralLedgerController::class)
            ->name('reports.general-ledger')
            ->middleware('can:reports.general-ledger.view');
        Route::get('reports/trial-balance', TrialBalanceController::class)
            ->name('reports.trial-balance')
            ->middleware('can:reports.trial-balance.view');
        Route::get('reports/balance-sheet', BalanceSheetController::class)
            ->name('reports.balance-sheet')
            ->middleware('can:reports.balance-sheet.view');
        Route::get('reports/income-statement', IncomeStatementController::class)
            ->name('reports.income-statement')
            ->middleware('can:reports.income-statement.view');
        Route::get('audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index')
            ->middleware('can:audit-logs.view');
    });
