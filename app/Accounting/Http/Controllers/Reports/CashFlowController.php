<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\CashFlowReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashFlowController extends Controller
{
    public function __invoke(Request $request, CashFlowReport $report): Response
    {
        return Inertia::render('accounting/reports/table', [
            'title' => 'Cash Flow',
            'rows' => $report->rows($request->only(['date_from', 'date_to'])),
            'exportBase' => '/accounting/reports/cash-flow/export',
        ]);
    }
}
