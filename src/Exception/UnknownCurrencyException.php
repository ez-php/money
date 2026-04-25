<?php

declare(strict_types=1);

namespace EzPhp\Money\Exception;

/**
 * Thrown when an ISO 4217 currency code is not found in the registry.
 */
final class UnknownCurrencyException extends MoneyException
{
    public function __construct(string $code)
    {
        parent::__construct(\sprintf('Unknown currency code: "%s"', $code));
    }
}
