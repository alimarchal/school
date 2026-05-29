<?php

use App\Accounting\Database\Seeders\AccountingDatabaseSeeder;
use App\Accounting\Models\AccountType;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\Currency;
use App\Accounting\Services\AccountingHealthCheckService;
use Illuminate\Support\Facades\Artisan;

it('seeds accounting data idempotently', function (): void {
    $this->seed(AccountingDatabaseSeeder::class);
    $this->seed(AccountingDatabaseSeeder::class);

    expect(AccountType::query()->count())->toBe(5)
        ->and(Currency::query()->where('is_base', true)->count())->toBe(1)
        ->and(ChartOfAccount::query()->where('account_code', '1101')->count())->toBe(1)
        ->and(ChartOfAccount::query()->where('account_code', '4101')->count())->toBe(1);
});

it('verifies a healthy accounting installation', function (): void {
    $this->seed(AccountingDatabaseSeeder::class);

    $result = app(AccountingHealthCheckService::class)->check();

    expect($result['ok'])->toBeTrue();
});

it('registers accounting artisan commands', function (): void {
    expect(Artisan::all())->toHaveKeys([
        'accounting:install',
        'accounting:seed',
        'accounting:sync-db-objects',
        'accounting:verify',
        'accounting:health-check',
        'accounting:rebuild-snapshots',
        'accounting:close-period',
        'accounting:open-period',
    ]);
});
