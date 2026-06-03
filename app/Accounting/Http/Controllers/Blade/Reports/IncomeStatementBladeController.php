<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\IncomeStatementReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeStatementBladeController extends Controller
{
    public function __invoke(Request $request, IncomeStatementReport $report): View
    {
        return view('accounting::reports.income-statement', [
            'rows' => $report->rows(),
        ]);
    }
}
