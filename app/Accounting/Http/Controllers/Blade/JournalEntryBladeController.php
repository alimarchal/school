<?php

namespace App\Accounting\Http\Controllers\Blade;

use App\Accounting\Actions\VoidJournalEntryAction;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\Currency;
use App\Accounting\Models\JournalEntry;
use App\Accounting\Services\JournalEntryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class JournalEntryBladeController extends Controller
{
    public function index(): View
    {
        $entries = QueryBuilder::for(JournalEntry::query()->with(['currency', 'accountingPeriod']))
            ->allowedFilters(
                AllowedFilter::partial('reference'),
                AllowedFilter::partial('description'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('currency_id'),
            )
            ->when(request('filter.entry_date_from'), fn ($q, $d) => $q->whereDate('entry_date', '>=', $d))
            ->when(request('filter.entry_date_to'), fn ($q, $d) => $q->whereDate('entry_date', '<=', $d))
            ->latest('entry_date')
            ->paginate(25)
            ->withQueryString();

        return view('accounting::journal-entries.index', [
            'entries' => $entries,
            'filters' => request()->input('filter', []),
            'currencies' => Currency::query()->where('is_active', true)->orderBy('code')->get(['id', 'code', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('accounting::journal-entries.create', [
            'entry' => null,
            'accounts' => ChartOfAccount::query()->where('is_group', false)->where('is_active', true)->orderBy('account_code')->get(['id', 'account_code', 'account_name']),
            'currencies' => Currency::query()->where('is_active', true)->orderByDesc('is_base')->orderBy('code')->get(['id', 'code', 'name', 'is_base']),
        ]);
    }

    public function show(JournalEntry $journalEntry): View
    {
        return view('accounting::journal-entries.show', [
            'entry' => $journalEntry->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod']),
        ]);
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
