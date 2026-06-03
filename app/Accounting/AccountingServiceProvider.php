<?php

namespace App\Accounting;

use App\Accounting\Console\Commands\AccountingCloseFiscalYearCommand;
use App\Accounting\Console\Commands\AccountingClosePeriodCommand;
use App\Accounting\Console\Commands\AccountingHealthCheckCommand;
use App\Accounting\Console\Commands\AccountingInstallCommand;
use App\Accounting\Console\Commands\AccountingOpenPeriodCommand;
use App\Accounting\Console\Commands\AccountingRebuildSnapshotsCommand;
use App\Accounting\Console\Commands\AccountingSeedCommand;
use App\Accounting\Console\Commands\AccountingSyncDatabaseObjectsCommand;
use App\Accounting\Console\Commands\AccountingVerifyCommand;
use App\Accounting\Http\Livewire\JournalEntryForm;
use App\Accounting\Http\Livewire\Reports\AgedPayablesLivewire;
use App\Accounting\Http\Livewire\Reports\AgedReceivablesLivewire;
use App\Accounting\Http\Livewire\Reports\BalanceSheetLivewire;
use App\Accounting\Http\Livewire\Reports\BankBookLivewire;
use App\Accounting\Http\Livewire\Reports\CashBookLivewire;
use App\Accounting\Http\Livewire\Reports\CashFlowLivewire;
use App\Accounting\Http\Livewire\Reports\GeneralLedgerLivewire;
use App\Accounting\Http\Livewire\Reports\IncomeStatementLivewire;
use App\Accounting\Http\Livewire\Reports\TrialBalanceLivewire;
use App\Accounting\Services\AccountingDatabaseObjectSynchronizer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('accounting.php'), 'accounting');

        $this->app->singleton(AccountingDatabaseObjectSynchronizer::class);
    }

    public function boot(): void
    {
        if (config('accounting.ui_driver') === 'blade') {
            $this->loadRoutesFrom(base_path('routes/accounting-blade.php'));
        } else {
            $this->loadRoutesFrom(base_path('routes/accounting.php'));
        }
        $this->loadRoutesFrom(base_path('routes/accounting-api.php'));

        $this->loadViewsFrom(resource_path('views/accounting'), 'accounting');

        Blade::anonymousComponentPath(resource_path('views/accounting/components'), 'accounting');

        if (class_exists(Livewire::class)) {
            Livewire::component('accounting::journal-entry-form', JournalEntryForm::class);
            Livewire::component('accounting::reports.general-ledger', GeneralLedgerLivewire::class);
            Livewire::component('accounting::reports.trial-balance', TrialBalanceLivewire::class);
            Livewire::component('accounting::reports.balance-sheet', BalanceSheetLivewire::class);
            Livewire::component('accounting::reports.income-statement', IncomeStatementLivewire::class);
            Livewire::component('accounting::reports.cash-flow', CashFlowLivewire::class);
            Livewire::component('accounting::reports.aged-payables', AgedPayablesLivewire::class);
            Livewire::component('accounting::reports.aged-receivables', AgedReceivablesLivewire::class);
            Livewire::component('accounting::reports.bank-book', BankBookLivewire::class);
            Livewire::component('accounting::reports.cash-book', CashBookLivewire::class);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                AccountingInstallCommand::class,
                AccountingSeedCommand::class,
                AccountingSyncDatabaseObjectsCommand::class,
                AccountingVerifyCommand::class,
                AccountingHealthCheckCommand::class,
                AccountingRebuildSnapshotsCommand::class,
                AccountingCloseFiscalYearCommand::class,
                AccountingClosePeriodCommand::class,
                AccountingOpenPeriodCommand::class,
            ]);
        }
    }
}
