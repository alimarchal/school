<?php

use App\Accounting\Http\Controllers\Api\AccountingPeriodApiController;
use App\Accounting\Http\Controllers\Api\AccountTypeApiController;
use App\Accounting\Http\Controllers\Api\BankAccountApiController;
use App\Accounting\Http\Controllers\Api\ChartOfAccountApiController;
use App\Accounting\Http\Controllers\Api\CostCenterApiController;
use App\Accounting\Http\Controllers\Api\CurrencyApiController;
use App\Accounting\Http\Controllers\Api\JournalEntryApiController;
use App\Accounting\Http\Controllers\Api\ReconciliationApiController;
use Illuminate\Support\Facades\Route;

$apiResourceRoutes = function (string $uri, string $controller, string $routeName, string $permissionPrefix): void {
    Route::get($uri, [$controller, 'index'])
        ->name("{$routeName}.index")
        ->middleware("can:{$permissionPrefix}.view");
    Route::post($uri, [$controller, 'store'])
        ->name("{$routeName}.store")
        ->middleware("can:{$permissionPrefix}.create");
    Route::get("{$uri}/{record}", [$controller, 'show'])
        ->name("{$routeName}.show")
        ->middleware("can:{$permissionPrefix}.view");
    Route::match(['put', 'patch'], "{$uri}/{record}", [$controller, 'update'])
        ->name("{$routeName}.update")
        ->middleware("can:{$permissionPrefix}.update");
    Route::delete("{$uri}/{record}", [$controller, 'destroy'])
        ->name("{$routeName}.destroy")
        ->middleware("can:{$permissionPrefix}.delete");
};

Route::middleware(['api', 'auth'])
    ->prefix(config('accounting.api_prefix', 'api/accounting/v1'))
    ->name('api.accounting.')
    ->group(function () use ($apiResourceRoutes): void {
        $apiResourceRoutes('account-types', AccountTypeApiController::class, 'account-types', 'account-types');
        $apiResourceRoutes('currencies', CurrencyApiController::class, 'currencies', 'currencies');
        $apiResourceRoutes('periods', AccountingPeriodApiController::class, 'periods', 'periods');

        Route::get('chart-of-accounts', [ChartOfAccountApiController::class, 'index'])
            ->name('chart-of-accounts.index')
            ->middleware('can:chart-of-accounts.view');
        Route::post('chart-of-accounts', [ChartOfAccountApiController::class, 'store'])
            ->name('chart-of-accounts.store')
            ->middleware('can:chart-of-accounts.create');
        Route::get('chart-of-accounts/{chartOfAccount}', [ChartOfAccountApiController::class, 'show'])
            ->name('chart-of-accounts.show')
            ->middleware('can:chart-of-accounts.view');
        Route::match(['put', 'patch'], 'chart-of-accounts/{chartOfAccount}', [ChartOfAccountApiController::class, 'update'])
            ->name('chart-of-accounts.update')
            ->middleware('can:chart-of-accounts.update');
        Route::delete('chart-of-accounts/{chartOfAccount}', [ChartOfAccountApiController::class, 'destroy'])
            ->name('chart-of-accounts.destroy')
            ->middleware('can:chart-of-accounts.delete');

        $apiResourceRoutes('cost-centers', CostCenterApiController::class, 'cost-centers', 'cost-centers');
        $apiResourceRoutes('bank-accounts', BankAccountApiController::class, 'bank-accounts', 'bank-accounts');
        $apiResourceRoutes('reconciliations', ReconciliationApiController::class, 'reconciliations', 'reconciliations');

        Route::get('journal-entries', [JournalEntryApiController::class, 'index'])
            ->name('journal-entries.index')
            ->middleware('can:journal-entries.view');
        Route::post('journal-entries', [JournalEntryApiController::class, 'store'])
            ->name('journal-entries.store')
            ->middleware('can:journal-entries.create');
        Route::get('journal-entries/{journalEntry}', [JournalEntryApiController::class, 'show'])
            ->name('journal-entries.show')
            ->middleware('can:journal-entries.view');
        Route::post('journal-entries/{journalEntry}/post', [JournalEntryApiController::class, 'post'])
            ->name('journal-entries.post')
            ->middleware('can:journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryApiController::class, 'reverse'])
            ->name('journal-entries.reverse')
            ->middleware('can:journal-entries.reverse');
        Route::post('journal-entries/{journalEntry}/void', [JournalEntryApiController::class, 'void'])
            ->name('journal-entries.void')
            ->middleware('can:journal-entries.void');
    });
