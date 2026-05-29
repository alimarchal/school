<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\Currency;

class CurrencyController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return Currency::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
