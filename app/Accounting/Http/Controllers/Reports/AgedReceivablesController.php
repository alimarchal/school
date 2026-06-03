<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\AgedReceivablesReport;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AgedReceivablesController extends Controller
{
    public function __invoke(AgedReceivablesReport $report): Response
    {
        return Inertia::render('accounting/reports/aged-receivables', [
            'rows' => $report->rows(),
        ]);
    }
}
