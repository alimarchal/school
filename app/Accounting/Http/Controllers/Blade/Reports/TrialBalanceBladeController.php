<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\TrialBalanceReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrialBalanceBladeController extends Controller
{
    public function __invoke(Request $request, TrialBalanceReport $report): View
    {
        return view('accounting::reports.trial-balance', [
            'rows' => $report->rows(),
            'totals' => $report->totals(),
        ]);
    }
}
