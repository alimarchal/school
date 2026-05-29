<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\BankAccount;

class BankAccountController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return BankAccount::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
