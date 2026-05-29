<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\BalanceSheetReport;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BalanceSheetController extends Controller
{
    public function __invoke(BalanceSheetReport $report): Response
    {
        return Inertia::render('accounting/reports/balance-sheet', [
            'rows' => $report->rows(),
        ]);
    }
}
