<?php

namespace App\Accounting\Http\Controllers\Api;

use App\Accounting\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class BankAccountApiController extends SimpleAccountingApiController
{
    protected function model(): string
    {
        return BankAccount::class;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255', Rule::unique('accounting_bank_accounts', 'account_number')->ignore($record?->getKey())],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'branch' => ['nullable', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:255'],
            'swift_code' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
