<?php

namespace App\Accounting\Http\Controllers\Api;

use App\Accounting\Actions\VoidJournalEntryAction;
use App\Accounting\Http\Requests\StoreJournalEntryRequest;
use App\Accounting\Http\Resources\JournalEntryResource;
use App\Accounting\Models\JournalEntry;
use App\Accounting\Services\JournalEntryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalEntryApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return JournalEntryResource::collection(
            JournalEntry::query()->with(['currency', 'accountingPeriod'])->latest('entry_date')->paginate()
        );
    }

    public function show(JournalEntry $journalEntry): JournalEntryResource
    {
        return JournalEntryResource::make(
            $journalEntry->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod'])
        );
    }

    public function store(StoreJournalEntryRequest $request, JournalEntryService $service): JournalEntryResource
    {
        return JournalEntryResource::make($service->create($request->validated()));
    }

    public function post(JournalEntry $journalEntry, JournalEntryService $service): JournalEntryResource
    {
        return JournalEntryResource::make(
            $service->post($journalEntry)->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod'])
        );
    }

    public function reverse(Request $request, JournalEntry $journalEntry, JournalEntryService $service): JournalEntryResource
    {
        return JournalEntryResource::make(
            $service->reverse($journalEntry, $request->string('description')->toString() ?: null)
                ->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod'])
        );
    }

    public function void(JournalEntry $journalEntry, VoidJournalEntryAction $action): JournalEntryResource
    {
        return JournalEntryResource::make(
            $action->execute($journalEntry)->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod'])
        );
    }
}
