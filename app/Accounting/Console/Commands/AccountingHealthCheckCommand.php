<?php

namespace App\Accounting\Console\Commands;

class AccountingHealthCheckCommand extends AccountingVerifyCommand
{
    protected $signature = 'accounting:health-check';

    protected $description = 'Run accounting module health checks.';
}
