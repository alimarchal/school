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

Route::middleware(['api', 'auth'])
    ->prefix(config('accounting.api_prefix', 'api/accounting/v1'))
    ->name('api.accounting.')
    ->group(function (): void {
        Route::apiResource('account-types', AccountTypeApiController::class);
        Route::apiResource('currencies', CurrencyApiController::class);
        Route::apiResource('periods', AccountingPeriodApiController::class);
        Route::apiResource('chart-of-accounts', ChartOfAccountApiController::class)
            ->parameters(['chart-of-accounts' => 'chartOfAccount']);
        Route::apiResource('cost-centers', CostCenterApiController::class);
        Route::apiResource('bank-accounts', BankAccountApiController::class);
        Route::apiResource('reconciliations', ReconciliationApiController::class);
        Route::apiResource('journal-entries', JournalEntryApiController::class)
            ->only(['index', 'store', 'show'])
            ->parameters(['journal-entries' => 'journalEntry']);
        Route::post('journal-entries/{journalEntry}/post', [JournalEntryApiController::class, 'post'])->name('journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryApiController::class, 'reverse'])->name('journal-entries.reverse');
        Route::post('journal-entries/{journalEntry}/void', [JournalEntryApiController::class, 'void'])->name('journal-entries.void');
    });
