<?php

namespace App\Accounting\Services;

use App\Accounting\Models\Currency;

class CurrencyConversionService
{
    public function toBase(float $amount, Currency $currency): float
    {
        return round($amount * (float) $currency->exchange_rate_to_base, 2);
    }

    public function fromBase(float $amount, Currency $currency): float
    {
        return round($amount / (float) $currency->exchange_rate_to_base, 2);
    }
}
