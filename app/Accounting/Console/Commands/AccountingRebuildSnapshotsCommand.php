<?php

namespace App\Accounting\Console\Commands;

use App\Accounting\Actions\CreateAccountBalanceSnapshotsAction;
use App\Accounting\Models\AccountingPeriod;
use Illuminate\Console\Command;

class AccountingRebuildSnapshotsCommand extends Command
{
    protected $signature = 'accounting:rebuild-snapshots {period_id?}';

    protected $description = 'Rebuild accounting balance snapshots.';

    public function handle(CreateAccountBalanceSnapshotsAction $action): int
    {
        $query = AccountingPeriod::query();

        if ($this->argument('period_id')) {
            $query->whereKey($this->argument('period_id'));
        }

        $query->each(fn (AccountingPeriod $period): null => $action->execute($period));

        $this->info('Accounting snapshots rebuilt.');

        return self::SUCCESS;
    }
}
