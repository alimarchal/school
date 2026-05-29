<?php

namespace App\Accounting\Console\Commands;

use App\Accounting\Services\AccountingHealthCheckService;
use Illuminate\Console\Command;

class AccountingVerifyCommand extends Command
{
    protected $signature = 'accounting:verify';

    protected $description = 'Verify the accounting module installation.';

    public function handle(AccountingHealthCheckService $healthCheck): int
    {
        $result = $healthCheck->check();

        foreach ($result as $key => $value) {
            $this->line($key.': '.json_encode($value));
        }

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
