<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\CashFlowReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashFlowBladeController extends Controller
{
    public function __invoke(Request $request, CashFlowReport $report): View
    {
        $filters = $request->only(['date_from', 'date_to']);

        return view('accounting::reports.cash-flow', [
            'rows' => $report->rows($filters),
            'filters' => $filters,
        ]);
    }
}
