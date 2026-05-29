<?php

namespace App\Accounting\Database\Objects;

interface AccountingDatabaseObjects
{
    public function sync(): void;

    public function drop(): void;
}
