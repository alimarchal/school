<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\AgedPayablesReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgedPayablesBladeController extends Controller
{
    public function __invoke(Request $request, AgedPayablesReport $report): View
    {
        return view('accounting::reports.aged-payables', [
            'rows' => $report->rows(),
        ]);
    }
}
