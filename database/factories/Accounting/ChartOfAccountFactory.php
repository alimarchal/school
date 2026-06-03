<?php

namespace Database\Factories\Accounting;

use App\Accounting\Models\AccountType;
use App\Accounting\Models\ChartOfAccount;
use App\Accounting\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChartOfAccount>
 */
class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'account_type_id' => AccountTypeFactory::new(),
            'currency_id' => CurrencyFactory::new()->base(),
            'account_code' => fake()->unique()->numerify('####'),
            'account_name' => fake()->words(3, true),
            'normal_balance' => fake()->randomElement(['debit', 'credit']),
            'description' => fake()->sentence(),
            'is_group' => false,
            'is_active' => true,
            'is_system' => false,
            'metadata' => null,
        ];
    }

    public function group(): static
    {
        return $this->state(['is_group' => true]);
    }

    public function posting(): static
    {
        return $this->state(['is_group' => false, 'is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function system(): static
    {
        return $this->state(['is_system' => true]);
    }

    public function debitNormal(): static
    {
        return $this->state(['normal_balance' => 'debit']);
    }

    public function creditNormal(): static
    {
        return $this->state(['normal_balance' => 'credit']);
    }

    public function withParent(ChartOfAccount $parent): static
    {
        return $this->state(['parent_id' => $parent->id]);
    }
}
