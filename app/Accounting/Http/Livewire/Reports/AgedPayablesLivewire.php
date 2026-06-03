<?php

namespace App\Accounting\Http\Livewire\Reports;

use App\Accounting\Reports\AgedPayablesReport;
use Illuminate\View\View;
use Livewire\Component;

class AgedPayablesLivewire extends Component
{
    public function render(AgedPayablesReport $report): View
    {
        return view('accounting::livewire.reports.aged-payables', [
            'rows' => $report->rows(),
        ]);
    }
}
