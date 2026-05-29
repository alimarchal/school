<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\AccountingAuditLog;

class AuditLogController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return AccountingAuditLog::class;
    }

    protected function page(): string
    {
        return 'accounting/simple-index';
    }
}
