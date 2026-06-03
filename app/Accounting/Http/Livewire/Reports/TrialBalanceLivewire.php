<?php

namespace App\Accounting\Http\Livewire\Reports;

use App\Accounting\Reports\TrialBalanceReport;
use Illuminate\View\View;
use Livewire\Component;

class TrialBalanceLivewire extends Component
{
    public function render(TrialBalanceReport $report): View
    {
        return view('accounting::livewire.reports.trial-balance', [
            'rows' => $report->rows(),
            'totals' => $report->totals(),
        ]);
    }
}
