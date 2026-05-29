<?php

namespace App\Accounting\Reports;

use Illuminate\Database\Query\Builder;

class CashBookReport
{
    public function query(): Builder
    {
        return app(GeneralLedgerReport::class)
            ->query()
            ->where('account_code', config('accounting.defaults.cash_account_code'));
    }
}
