<?php

use App\Accounting\Http\Controllers\Api\ChartOfAccountApiController;
use App\Accounting\Http\Controllers\Api\JournalEntryApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth'])
    ->prefix(config('accounting.api_prefix', 'api/accounting/v1'))
    ->name('api.accounting.')
    ->group(function (): void {
        Route::get('chart-of-accounts', [ChartOfAccountApiController::class, 'index'])->name('chart-of-accounts.index');
        Route::get('journal-entries', [JournalEntryApiController::class, 'index'])->name('journal-entries.index');
        Route::post('journal-entries', [JournalEntryApiController::class, 'store'])->name('journal-entries.store');
    });
