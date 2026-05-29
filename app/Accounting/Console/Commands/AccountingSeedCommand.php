<?php

namespace App\Accounting\Console\Commands;

use App\Accounting\Database\Seeders\AccountingDatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AccountingSeedCommand extends Command
{
    protected $signature = 'accounting:seed';

    protected $description = 'Seed the accounting module data safely.';

    public function handle(): int
    {
        Artisan::call('db:seed', [
            '--class' => AccountingDatabaseSeeder::class,
            '--no-interaction' => true,
        ], $this->output);

        return self::SUCCESS;
    }
}
