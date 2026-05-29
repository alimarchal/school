<?php

namespace App\Accounting\Console\Commands;

use App\Accounting\Actions\CloseFiscalYearAction;
use App\Accounting\Models\AccountingPeriod;
use Illuminate\Console\Command;

class AccountingCloseFiscalYearCommand extends Command
{
    protected $signature = 'accounting:close-fiscal-year {period_id}';

    protected $description = 'Close an accounting period as a fiscal year and transfer income statement balances to retained earnings.';

    public function handle(CloseFiscalYearAction $action): int
    {
        $period = AccountingPeriod::query()->findOrFail($this->argument('period_id'));
        $closed = $action->execute($period);

        $this->info("Fiscal year closed for {$closed->name}.");

        return self::SUCCESS;
    }
}
