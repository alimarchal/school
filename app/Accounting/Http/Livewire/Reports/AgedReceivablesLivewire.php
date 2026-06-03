<?php

namespace App\Accounting\Http\Livewire\Reports;

use App\Accounting\Reports\AgedReceivablesReport;
use Illuminate\View\View;
use Livewire\Component;

class AgedReceivablesLivewire extends Component
{
    public function render(AgedReceivablesReport $report): View
    {
        return view('accounting::livewire.reports.aged-receivables', [
            'rows' => $report->rows(),
        ]);
    }
}
