<?php

namespace App\Accounting\Http\Requests;

class UpdateJournalEntryRequest extends StoreJournalEntryRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('journal-entries.update') ?? false;
    }
}
