<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\Reconciliation;

class ReconciliationController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return Reconciliation::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
