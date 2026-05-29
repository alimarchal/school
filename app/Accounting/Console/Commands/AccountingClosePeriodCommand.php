<?php

namespace App\Accounting\Console\Commands;

use App\Accounting\Actions\CloseAccountingPeriodAction;
use App\Accounting\Models\AccountingPeriod;
use Illuminate\Console\Command;

class AccountingClosePeriodCommand extends Command
{
    protected $signature = 'accounting:close-period {period_id}';

    protected $description = 'Close an accounting period.';

    public function handle(CloseAccountingPeriodAction $action): int
    {
        $action->execute(AccountingPeriod::query()->findOrFail($this->argument('period_id')));
        $this->info('Accounting period closed.');

        return self::SUCCESS;
    }
}
