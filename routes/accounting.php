<?php

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
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix(config('accounting.route_prefix', 'accounting'))
    ->name('accounting.')
    ->group(function (): void {
        Route::get('/', AccountingDashboardController::class)->name('dashboard');

        Route::resource('account-types', AccountTypeController::class)->only(['index']);
        Route::resource('currencies', CurrencyController::class)->only(['index']);
        Route::resource('periods', AccountingPeriodController::class)->only(['index']);
        Route::get('chart-of-accounts/tree', [ChartOfAccountController::class, 'tree'])->name('chart-of-accounts.tree');
        Route::resource('chart-of-accounts', ChartOfAccountController::class)->only(['index']);
        Route::resource('cost-centers', CostCenterController::class)->only(['index']);
        Route::resource('bank-accounts', BankAccountController::class)->only(['index']);
        Route::resource('reconciliations', ReconciliationController::class)->only(['index']);
        Route::resource('journal-entries', JournalEntryController::class)
            ->only(['index', 'show', 'store'])
            ->parameters(['journal-entries' => 'journalEntry']);
        Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
        Route::post('journal-entries/{journalEntry}/void', [JournalEntryController::class, 'void'])->name('journal-entries.void');

        Route::get('reports/general-ledger', GeneralLedgerController::class)->name('reports.general-ledger');
        Route::get('reports/trial-balance', TrialBalanceController::class)->name('reports.trial-balance');
        Route::get('reports/balance-sheet', BalanceSheetController::class)->name('reports.balance-sheet');
        Route::get('reports/income-statement', IncomeStatementController::class)->name('reports.income-statement');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
