<?php

namespace App\Accounting\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends AccountingModel
{
    protected $table = 'accounting_account_types';

    protected $fillable = [
        'code',
        'name',
        'normal_balance',
        'report_group',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'account_type_id');
    }
}
