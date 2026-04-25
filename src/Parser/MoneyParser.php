<?php

declare(strict_types=1);

namespace EzPhp\Money\Parser;

use EzPhp\Money\Currency;
use EzPhp\Money\Money;

/**
 * Parses a string into a Money value.
 */
interface MoneyParser
{
    /**
     * Parse a money string and return a Money value.
     *
     * $currency is used as the target currency. Implementations may
     * detect the currency from the string and ignore this parameter
     * (e.g. IntlMoneyParser), or may require it (e.g. DecimalMoneyParser).
     *
     * @throws \InvalidArgumentException if the string cannot be parsed
     * @throws \EzPhp\Money\Exception\UnknownCurrencyException if the currency code is unrecognised
     */
    public function parse(string $value, Currency|string $currency): Money;
}
