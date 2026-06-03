<?php

namespace App\Accounting\Http\Controllers\Reports;

use App\Accounting\Reports\CashBookReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashBookController extends Controller
{
    public function __invoke(Request $request, CashBookReport $report): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'account_id', 'status']);

        return Inertia::render('accounting/reports/cash-book', [
            'entries' => $report->query($filters)->paginate(100)->withQueryString(),
            'totals' => $report->totals($filters),
            'filters' => $filters,
        ]);
    }
}
