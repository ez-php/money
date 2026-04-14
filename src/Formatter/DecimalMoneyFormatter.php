<?php

declare(strict_types=1);

namespace EzPhp\Money\Formatter;

use EzPhp\Money\Money;

/**
 * Formats a Money value as a plain decimal string.
 *
 * By default the output is just the amount (e.g. "10.50").
 * Pass true to the constructor to include the currency code (e.g. "10.50 EUR").
 */
final class DecimalMoneyFormatter implements MoneyFormatter
{
    public function __construct(
        private readonly bool $includeCurrencyCode = false,
    ) {
    }

    /**
     * Format the given Money value as a decimal string.
     *
     * @return string E.g. "10.50" or "10.50 EUR" when includeCurrencyCode is true
     */
    public function format(Money $money): string
    {
        $amount = $money->getAmount()->toString();

        if ($this->includeCurrencyCode) {
            return $amount . ' ' . $money->getCurrency()->getCode();
        }

        return $amount;
    }
}
