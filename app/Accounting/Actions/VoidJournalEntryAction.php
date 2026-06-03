<?php

namespace App\Accounting\Actions;

use App\Accounting\Models\JournalEntry;
use InvalidArgumentException;

class VoidJournalEntryAction
{
    public function execute(JournalEntry $journalEntry): JournalEntry
    {
        if ($journalEntry->status === 'posted') {
            throw new InvalidArgumentException('Posted journal entries must be reversed instead of voided.');
        }

        if ($journalEntry->status === 'void') {
            throw new InvalidArgumentException('Journal entry is already voided.');
        }

        $journalEntry->forceFill(['status' => 'void'])->save();

        return $journalEntry->refresh();
    }
}
