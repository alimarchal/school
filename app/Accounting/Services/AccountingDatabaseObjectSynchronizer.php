<?php

namespace App\Accounting\Services;

use App\Accounting\Database\Objects\AccountingDatabaseObjects;
use App\Accounting\Database\Objects\MariaDbAccountingDatabaseObjects;
use App\Accounting\Database\Objects\MySqlAccountingDatabaseObjects;
use App\Accounting\Database\Objects\PostgresAccountingDatabaseObjects;
use App\Accounting\Database\Objects\SqliteAccountingDatabaseObjects;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccountingDatabaseObjectSynchronizer
{
    public function sync(): void
    {
        $this->driver()->sync();
    }

    public function drop(): void
    {
        $this->driver()->drop();
    }

    private function driver(): AccountingDatabaseObjects
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => app(PostgresAccountingDatabaseObjects::class),
            'mysql' => app(MySqlAccountingDatabaseObjects::class),
            'mariadb' => app(MariaDbAccountingDatabaseObjects::class),
            'sqlite' => app(SqliteAccountingDatabaseObjects::class),
            default => throw new InvalidArgumentException('Unsupported accounting database driver.'),
        };
    }
}
