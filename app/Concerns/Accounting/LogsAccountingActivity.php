<?php

namespace App\Concerns\Accounting;

use Spatie\Activitylog\Support\LogOptions;

trait LogsAccountingActivity
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('accounting')
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
