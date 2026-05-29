<?php

namespace App\Accounting\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends AccountingModel
{
    use SoftDeletes;

    protected $table = 'accounting_bank_accounts';

    protected $fillable = [
        'chart_of_account_id',
        'account_name',
        'account_number',
        'bank_name',
        'branch',
        'iban',
        'swift_code',
        'is_active',
        'description',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
