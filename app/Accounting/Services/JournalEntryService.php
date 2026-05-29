<?php

namespace App\Accounting\Services;

use App\Accounting\Actions\PostJournalEntryAction;
use App\Accounting\Actions\ReverseJournalEntryAction;
use App\Accounting\Models\Currency;
use App\Accounting\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class JournalEntryService
{
    /**
     * @param  array{entry_date: string, currency_id?: int, reference?: string|null, description?: string|null, lines: array<int, array<string, mixed>>, auto_post?: bool}  $data
     */
    public function create(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data): JournalEntry {
            $currencyId = $data['currency_id']
                ?? Currency::query()->where('is_base', true)->value('id');

            $journalEntry = JournalEntry::query()->create([
                'entry_date' => $data['entry_date'],
                'currency_id' => $currencyId,
                'fx_rate_to_base' => $data['fx_rate_to_base'] ?? 1,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'draft',
            ]);

            foreach (array_values($data['lines']) as $index => $line) {
                $journalEntry->lines()->create([
                    'line_no' => $index + 1,
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'cost_center_id' => $line['cost_center_id'] ?? null,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            if (($data['auto_post'] ?? false) === true) {
                app(PostJournalEntryAction::class)->execute($journalEntry);
            }

            return $journalEntry->refresh()->load(['lines.account', 'currency', 'accountingPeriod']);
        });
    }

    /**
     * @param  array{entry_date: string, currency_id?: int, reference?: string|null, description?: string|null, lines: array<int, array<string, mixed>>, auto_post?: bool}  $data
     */
    public function updateDraft(JournalEntry $journalEntry, array $data): JournalEntry
    {
        if ($journalEntry->status !== 'draft') {
            throw new \DomainException('Only draft journal entries can be edited.');
        }

        return DB::transaction(function () use ($journalEntry, $data): JournalEntry {
            $currencyId = $data['currency_id']
                ?? Currency::query()->where('is_base', true)->value('id');

            $journalEntry->update([
                'entry_date' => $data['entry_date'],
                'currency_id' => $currencyId,
                'fx_rate_to_base' => $data['fx_rate_to_base'] ?? 1,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            $journalEntry->lines()->delete();

            foreach (array_values($data['lines']) as $index => $line) {
                $journalEntry->lines()->create([
                    'line_no' => $index + 1,
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'cost_center_id' => $line['cost_center_id'] ?? null,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            if (($data['auto_post'] ?? false) === true) {
                app(PostJournalEntryAction::class)->execute($journalEntry);
            }

            return $journalEntry->refresh()->load(['lines.account', 'lines.costCenter', 'currency', 'accountingPeriod']);
        });
    }

    public function post(JournalEntry $journalEntry): JournalEntry
    {
        return app(PostJournalEntryAction::class)->execute($journalEntry);
    }

    public function reverse(JournalEntry $journalEntry, ?string $description = null): JournalEntry
    {
        return app(ReverseJournalEntryAction::class)->execute($journalEntry, $description);
    }
}
