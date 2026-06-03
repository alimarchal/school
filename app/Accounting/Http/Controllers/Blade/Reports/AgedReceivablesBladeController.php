<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\AgedReceivablesReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgedReceivablesBladeController extends Controller
{
    public function __invoke(Request $request, AgedReceivablesReport $report): View
    {
        return view('accounting::reports.aged-receivables', [
            'rows' => $report->rows(),
        ]);
    }
}
