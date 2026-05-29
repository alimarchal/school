<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\CostCenter;

class CostCenterController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return CostCenter::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
