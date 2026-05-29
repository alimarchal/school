<?php

use App\Accounting\AccountingServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    AccountingServiceProvider::class,
    FortifyServiceProvider::class,
];
