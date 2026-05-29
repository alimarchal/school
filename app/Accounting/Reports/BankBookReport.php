<?php

namespace App\Accounting\Reports;

use Illuminate\Database\Query\Builder;

class BankBookReport
{
    public function query(): Builder
    {
        return app(GeneralLedgerReport::class)
            ->query()
            ->where('account_code', config('accounting.defaults.bank_account_code'));
    }
}
