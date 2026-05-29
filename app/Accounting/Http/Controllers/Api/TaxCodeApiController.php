<?php

namespace App\Accounting\Http\Controllers\Api;

use App\Accounting\Models\TaxCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class TaxCodeApiController extends SimpleAccountingApiController
{
    protected function model(): string
    {
        return TaxCode::class;
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('accounting_tax_codes', 'code')->ignore($record?->getKey())],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
