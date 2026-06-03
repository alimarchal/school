<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\AgedPayablesReport;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AgedPayablesController extends Controller
{
    public function __invoke(AgedPayablesReport $report): Response
    {
        return Inertia::render('accounting/reports/aged-payables', [
            'rows' => $report->rows(),
        ]);
    }
}
