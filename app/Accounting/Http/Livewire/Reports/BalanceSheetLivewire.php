<?php

namespace App\Accounting\Http\Livewire\Reports;

use App\Accounting\Reports\BalanceSheetReport;
use Illuminate\View\View;
use Livewire\Component;

class BalanceSheetLivewire extends Component
{
    public function render(BalanceSheetReport $report): View
    {
        return view('accounting::livewire.reports.balance-sheet', [
            'rows' => $report->rows(),
        ]);
    }
}
