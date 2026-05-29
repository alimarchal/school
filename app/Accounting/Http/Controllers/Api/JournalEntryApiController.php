<?php

namespace App\Accounting\Http\Controllers\Api;

use App\Accounting\Http\Requests\StoreJournalEntryRequest;
use App\Accounting\Http\Resources\JournalEntryResource;
use App\Accounting\Models\JournalEntry;
use App\Accounting\Services\JournalEntryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalEntryApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return JournalEntryResource::collection(
            JournalEntry::query()->with(['currency', 'accountingPeriod'])->latest('entry_date')->paginate()
        );
    }

    public function store(StoreJournalEntryRequest $request, JournalEntryService $service): JournalEntryResource
    {
        return JournalEntryResource::make($service->create($request->validated()));
    }
}
