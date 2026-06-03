<?php

namespace App\Accounting\Models;

use Database\Factories\Accounting\CostCenterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCenter extends AccountingModel
{
    /** @use HasFactory<CostCenterFactory> */
    use HasFactory;

    protected static string $factory = CostCenterFactory::class;

    protected $table = 'accounting_cost_centers';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'type',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('code');
    }
}
