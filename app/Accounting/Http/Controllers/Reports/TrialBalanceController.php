<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\TrialBalanceReport;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class TrialBalanceController extends Controller
{
    public function __invoke(TrialBalanceReport $report): Response
    {
        return Inertia::render('accounting/reports/trial-balance', [
            'rows' => $report->rows(),
            'totals' => $report->totals(),
        ]);
    }
}
