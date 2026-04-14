<?php

declare(strict_types=1);

namespace EzPhp\Money\Formatter;

use EzPhp\Money\Money;

/**
 * Formats a Money value as a human-readable string.
 */
interface MoneyFormatter
{
    /**
     * Format the given Money value as a string.
     */
    public function format(Money $money): string;
}
