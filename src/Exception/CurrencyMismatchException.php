<?php

declare(strict_types=1);

namespace EzPhp\Money\Exception;

/**
 * Thrown when arithmetic is attempted between Money values of different currencies.
 */
final class CurrencyMismatchException extends MoneyException
{
    public function __construct(string $expected, string $actual)
    {
        parent::__construct(
            \sprintf('Currency mismatch: expected %s, got %s', $expected, $actual),
        );
    }
}
