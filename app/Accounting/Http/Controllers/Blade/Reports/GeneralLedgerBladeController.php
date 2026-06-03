<?php

namespace App\Accounting\Http\Controllers\Blade\Reports;

use App\Accounting\Reports\GeneralLedgerReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralLedgerBladeController extends Controller
{
    public function __invoke(Request $request, GeneralLedgerReport $report): View
    {
        $filters = $request->only(['date_from', 'date_to', 'account_id', 'status']);

        return view('accounting::reports.general-ledger', [
            'entries' => $report->query($filters)->paginate(100)->withQueryString(),
            'totals' => $report->totals($filters),
            'filters' => $filters,
        ]);
    }
}
