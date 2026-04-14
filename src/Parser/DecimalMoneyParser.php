<?php

declare(strict_types=1);

namespace EzPhp\Money\Parser;

use EzPhp\BigNum\RoundingMode;
use EzPhp\Money\Currency;
use EzPhp\Money\Money;

/**
 * Parses a plain decimal string into a Money value.
 *
 * The string must be a valid decimal number (e.g. "10.50", "-3.99", "1000").
 * The currency must be supplied explicitly because a plain decimal carries no
 * currency information.
 *
 * If the string has more decimal places than the currency allows, it is rounded
 * to currency scale using the supplied rounding mode (default: HALF_UP).
 */
final class DecimalMoneyParser implements MoneyParser
{
    public function __construct(
        private readonly RoundingMode $roundingMode = RoundingMode::HALF_UP,
    ) {
    }

    /**
     * Parse a plain decimal string and return a Money value.
     *
     * @throws \InvalidArgumentException if $value is not a valid decimal
     * @throws \EzPhp\Money\Exception\UnknownCurrencyException if the currency code is unrecognised
     */
    public function parse(string $value, Currency|string $currency): Money
    {
        $trimmed = \trim($value);

        if (!\preg_match('/^-?[0-9]+(?:\.[0-9]+)?$/', $trimmed)) {
            throw new \InvalidArgumentException(
                \sprintf('Cannot parse "%s" as a decimal money value', $value),
            );
        }

        return Money::of($trimmed, $currency, $this->roundingMode);
    }
}
