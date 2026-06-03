<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\BalanceSheetReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BalanceSheetBladeController extends Controller
{
    public function __invoke(Request $request, BalanceSheetReport $report): View
    {
        return view('accounting::reports.balance-sheet', [
            'rows' => $report->rows(),
        ]);
    }
}
