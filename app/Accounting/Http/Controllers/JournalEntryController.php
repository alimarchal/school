<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Actions\VoidJournalEntryAction;
use App\Accounting\Http\Requests\StoreJournalEntryRequest;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\CostCenter;
use App\Accounting\Models\Currency;
use App\Accounting\Models\JournalEntry;
use App\Accounting\Services\JournalEntryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JournalEntryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('accounting/journal-entries/index', [
            'entries' => JournalEntry::query()
                ->with(['currency', 'accountingPeriod'])
                ->latest('entry_date')
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('accounting/journal-entries/form', [
            'action' => route('accounting.journal-entries.store'),
            'accounts' => ChartOfAccount::query()
                ->where('is_group', false)
                ->where('is_active', true)
                ->orderBy('account_code')
                ->get(['id', 'account_code', 'account_name']),
            'currencies' => Currency::query()
                ->where('is_active', true)
                ->orderByDesc('is_base')
                ->orderBy('code')
                ->get(['id', 'code', 'name', 'is_base']),
            'costCenters' => CostCenter::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get(['id', 'code', 'name']),
        ]);
    }

    public function show(JournalEntry $journalEntry): Response
    {
        return Inertia::render('accounting/journal-entries/show', [
            'entry' => $journalEntry->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod']),
        ]);
    }

    public function store(StoreJournalEntryRequest $request, JournalEntryService $service): RedirectResponse
    {
        $entry = $service->create($request->validated());

        return to_route('accounting.journal-entries.show', $entry)->with('success', 'Journal entry created.');
    }

    public function post(JournalEntry $journalEntry, JournalEntryService $service): RedirectResponse
    {
        $service->post($journalEntry);

        return back()->with('success', 'Journal entry posted.');
    }

    public function reverse(Request $request, JournalEntry $journalEntry, JournalEntryService $service): RedirectResponse
    {
        $service->reverse($journalEntry, $request->string('description')->toString() ?: null);

        return back()->with('success', 'Journal entry reversed.');
    }

    public function void(JournalEntry $journalEntry, VoidJournalEntryAction $action): RedirectResponse
    {
        $action->execute($journalEntry);

        return back()->with('success', 'Journal entry voided.');
    }
}
