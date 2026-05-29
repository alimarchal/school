<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\IncomeStatementReport;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IncomeStatementController extends Controller
{
    public function __invoke(IncomeStatementReport $report): Response
    {
        return Inertia::render('accounting/reports/income-statement', [
            'rows' => $report->rows(),
        ]);
    }
}
