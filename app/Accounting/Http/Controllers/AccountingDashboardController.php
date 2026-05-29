<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\AccountType;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\Currency;
use App\Accounting\Models\JournalEntry;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountingDashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('accounting/dashboard', [
            'summary' => [
                'accountTypes' => AccountType::query()->count(),
                'currencies' => Currency::query()->count(),
                'accounts' => ChartOfAccount::query()->count(),
                'postedJournalEntries' => JournalEntry::query()->where('status', 'posted')->count(),
            ],
        ]);
    }
}
