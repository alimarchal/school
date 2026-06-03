<?php

namespace App\Accounting\Http\Livewire\Reports;

use App\Accounting\Reports\IncomeStatementReport;
use Illuminate\View\View;
use Livewire\Component;

class IncomeStatementLivewire extends Component
{
    public function render(IncomeStatementReport $report): View
    {
        return view('accounting::livewire.reports.income-statement', [
            'rows' => $report->rows(),
        ]);
    }
}
