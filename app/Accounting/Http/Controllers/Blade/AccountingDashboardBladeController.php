<?php

namespace App\Accounting\Http\Controllers\Blade;

use App\Accounting\Models\AccountType;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\Currency;
use App\Accounting\Models\JournalEntry;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AccountingDashboardBladeController extends Controller
{
    public function __invoke(): View
    {
        return view('accounting::dashboard', [
            'summary' => [
                'accountTypes' => AccountType::query()->count(),
                'currencies' => Currency::query()->count(),
                'accounts' => ChartOfAccount::query()->count(),
                'postedJournalEntries' => JournalEntry::query()->where('status', 'posted')->count(),
            ],
        ]);
    }
}
