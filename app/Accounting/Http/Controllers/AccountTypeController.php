<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\AccountType;

class AccountTypeController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return AccountType::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
