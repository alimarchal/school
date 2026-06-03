<?php

namespace App\Accounting\Http\Controllers\Blade;

use App\Accounting\Models\AccountingAuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuditLogBladeController extends Controller
{
    public function index(Request $request): View
    {
        $auditLogs = QueryBuilder::for(AccountingAuditLog::query())
            ->allowedFilters(
                AllowedFilter::partial('table_name'),
                AllowedFilter::exact('action'),
            )
            ->defaultSort('-id')
            ->paginate(25)
            ->withQueryString();

        return view('accounting::audit-logs.index', [
            'auditLogs' => $auditLogs,
        ]);
    }

    public function show(AccountingAuditLog $record): View
    {
        return view('accounting::audit-logs.show', [
            'auditLog' => $record,
        ]);
    }
}
