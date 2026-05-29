<?php

namespace App\Accounting;

use App\Accounting\Console\Commands\AccountingClosePeriodCommand;
use App\Accounting\Console\Commands\AccountingHealthCheckCommand;
use App\Accounting\Console\Commands\AccountingInstallCommand;
use App\Accounting\Console\Commands\AccountingOpenPeriodCommand;
use App\Accounting\Console\Commands\AccountingRebuildSnapshotsCommand;
use App\Accounting\Console\Commands\AccountingSeedCommand;
use App\Accounting\Console\Commands\AccountingSyncDatabaseObjectsCommand;
use App\Accounting\Console\Commands\AccountingVerifyCommand;
use App\Accounting\Services\AccountingDatabaseObjectSynchronizer;
use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('accounting.php'), 'accounting');

        $this->app->singleton(AccountingDatabaseObjectSynchronizer::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/accounting.php'));
        $this->loadRoutesFrom(base_path('routes/accounting-api.php'));

        if ($this->app->runningInConsole()) {
            $this->commands([
                AccountingInstallCommand::class,
                AccountingSeedCommand::class,
                AccountingSyncDatabaseObjectsCommand::class,
                AccountingVerifyCommand::class,
                AccountingHealthCheckCommand::class,
                AccountingRebuildSnapshotsCommand::class,
                AccountingClosePeriodCommand::class,
                AccountingOpenPeriodCommand::class,
            ]);
        }
    }
}
