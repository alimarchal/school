<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\AccountingPeriod;

class AccountingPeriodController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return AccountingPeriod::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
