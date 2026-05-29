<?php

namespace App\Accounting\Database\Seeders;

use App\Accounting\Models\AccountType;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\Currency;
use Illuminate\Database\Seeder;

class AccountingChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $baseCurrency = Currency::query()->where('is_base', true)->firstOrFail();
        $types = AccountType::query()->pluck('id', 'code');

        foreach ($this->accounts() as $account) {
            $parentId = null;

            if ($account['parent_code'] !== null) {
                $parentId = ChartOfAccount::query()
                    ->where('account_code', $account['parent_code'])
                    ->value('id');
            }

            ChartOfAccount::query()->updateOrCreate(
                ['account_code' => $account['account_code']],
                [
                    'parent_id' => $parentId,
                    'account_type_id' => $types[$account['type_code']],
                    'currency_id' => $baseCurrency->id,
                    'account_name' => $account['account_name'],
                    'normal_balance' => $account['normal_balance'],
                    'description' => $account['description'] ?? null,
                    'is_group' => $account['is_group'],
                    'is_active' => true,
                    'is_system' => true,
                ]
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function accounts(): array
    {
        return [
            ['account_code' => '1000', 'parent_code' => null, 'type_code' => 'ASSET', 'account_name' => 'Assets', 'normal_balance' => 'debit', 'is_group' => true],
            ['account_code' => '1100', 'parent_code' => '1000', 'type_code' => 'ASSET', 'account_name' => 'Current Assets', 'normal_balance' => 'debit', 'is_group' => true],
            ['account_code' => '1101', 'parent_code' => '1100', 'type_code' => 'ASSET', 'account_name' => 'Cash In Hand', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '1102', 'parent_code' => '1100', 'type_code' => 'ASSET', 'account_name' => 'Bank Accounts', 'normal_balance' => 'debit', 'is_group' => true],
            ['account_code' => '1103', 'parent_code' => '1100', 'type_code' => 'ASSET', 'account_name' => 'Accounts Receivable', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '1200', 'parent_code' => '1000', 'type_code' => 'ASSET', 'account_name' => 'Fixed Assets', 'normal_balance' => 'debit', 'is_group' => true],
            ['account_code' => '1201', 'parent_code' => '1200', 'type_code' => 'ASSET', 'account_name' => 'Furniture and Fixtures', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '1202', 'parent_code' => '1200', 'type_code' => 'ASSET', 'account_name' => 'Computer Equipment', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '2000', 'parent_code' => null, 'type_code' => 'LIABILITY', 'account_name' => 'Liabilities', 'normal_balance' => 'credit', 'is_group' => true],
            ['account_code' => '2100', 'parent_code' => '2000', 'type_code' => 'LIABILITY', 'account_name' => 'Current Liabilities', 'normal_balance' => 'credit', 'is_group' => true],
            ['account_code' => '2101', 'parent_code' => '2100', 'type_code' => 'LIABILITY', 'account_name' => 'Accounts Payable', 'normal_balance' => 'credit', 'is_group' => false],
            ['account_code' => '2102', 'parent_code' => '2100', 'type_code' => 'LIABILITY', 'account_name' => 'Accrued Expenses', 'normal_balance' => 'credit', 'is_group' => false],
            ['account_code' => '3000', 'parent_code' => null, 'type_code' => 'EQUITY', 'account_name' => 'Equity', 'normal_balance' => 'credit', 'is_group' => true],
            ['account_code' => '3101', 'parent_code' => '3000', 'type_code' => 'EQUITY', 'account_name' => 'Retained Earnings', 'normal_balance' => 'credit', 'is_group' => false],
            ['account_code' => '3102', 'parent_code' => '3000', 'type_code' => 'EQUITY', 'account_name' => 'Opening Balance Equity', 'normal_balance' => 'credit', 'is_group' => false],
            ['account_code' => '4000', 'parent_code' => null, 'type_code' => 'INCOME', 'account_name' => 'Income', 'normal_balance' => 'credit', 'is_group' => true],
            ['account_code' => '4100', 'parent_code' => '4000', 'type_code' => 'INCOME', 'account_name' => 'Operating Income', 'normal_balance' => 'credit', 'is_group' => true],
            ['account_code' => '4101', 'parent_code' => '4100', 'type_code' => 'INCOME', 'account_name' => 'Tuition Fee Income', 'normal_balance' => 'credit', 'is_group' => false],
            ['account_code' => '4102', 'parent_code' => '4100', 'type_code' => 'INCOME', 'account_name' => 'Admission Fee Income', 'normal_balance' => 'credit', 'is_group' => false],
            ['account_code' => '5000', 'parent_code' => null, 'type_code' => 'EXPENSE', 'account_name' => 'Expenses', 'normal_balance' => 'debit', 'is_group' => true],
            ['account_code' => '5100', 'parent_code' => '5000', 'type_code' => 'EXPENSE', 'account_name' => 'Operating Expenses', 'normal_balance' => 'debit', 'is_group' => true],
            ['account_code' => '5101', 'parent_code' => '5100', 'type_code' => 'EXPENSE', 'account_name' => 'Salary Expense', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '5102', 'parent_code' => '5100', 'type_code' => 'EXPENSE', 'account_name' => 'Rent Expense', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '5103', 'parent_code' => '5100', 'type_code' => 'EXPENSE', 'account_name' => 'Utilities Expense', 'normal_balance' => 'debit', 'is_group' => false],
            ['account_code' => '5201', 'parent_code' => '5000', 'type_code' => 'EXPENSE', 'account_name' => 'Rounding Difference', 'normal_balance' => 'debit', 'is_group' => false],
        ];
    }
}
