<?php

namespace App\Concerns\Accounting;

use Illuminate\Support\Facades\Gate;

trait HasAccountingPermissions
{
    protected function authorizeAccounting(string $permission): void
    {
        Gate::authorize($permission);
    }
}
