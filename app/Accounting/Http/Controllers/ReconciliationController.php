<?php

namespace App\Accounting\Http\Controllers;

use App\Accounting\Models\Reconciliation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ReconciliationController extends SimpleAccountingResourceController
{
    protected function model(): string
    {
        return Reconciliation::class;
    }

    protected function routeName(): string
    {
        return 'reconciliations';
    }

    protected function title(): string
    {
        return 'Reconciliation';
    }

    protected function fields(): array
    {
        return [
            ['name' => 'bank_account_id', 'label' => 'Bank Account ID', 'type' => 'number', 'table' => true],
            ['name' => 'statement_date', 'label' => 'Statement Date', 'type' => 'date', 'table' => true],
            ['name' => 'statement_balance', 'label' => 'Statement Balance', 'type' => 'number', 'step' => '0.01', 'table' => true],
            ['name' => 'book_balance', 'label' => 'Book Balance', 'type' => 'number', 'step' => '0.01', 'table' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'completed' => 'Completed', 'void' => 'Void'], 'table' => true],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'bank_account_id' => ['required', 'exists:accounting_bank_accounts,id'],
            'statement_date' => ['required', 'date'],
            'statement_balance' => ['required', 'numeric'],
            'book_balance' => ['required', 'numeric'],
            'status' => ['required', Rule::in(['draft', 'completed', 'void'])],
        ];
    }
}
