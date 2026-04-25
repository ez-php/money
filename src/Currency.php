<?php

declare(strict_types=1);

namespace EzPhp\Money;

use EzPhp\Money\Exception\UnknownCurrencyException;

/**
 * ISO 4217 currency value object.
 *
 * Instances are obtained via Currency::of() which delegates to the CurrencyRegistry.
 * Two Currency instances with the same ISO 4217 code are always considered equal.
 */
final class Currency implements \Stringable
{
    private function __construct(
        private readonly string $code,
        private readonly string $numericCode,
        private readonly string $name,
        private readonly int $scale,
        private readonly string $symbol,
    ) {
    }

    /**
     * Create a Currency from an ISO 4217 alphabetic code (e.g. "EUR", "USD").
     *
     * @throws UnknownCurrencyException if the code is not in the registry
     */
    public static function of(string $code): self
    {
        return CurrencyRegistry::get($code);
    }

    /**
     * Create a Currency instance from raw data.
     *
     * @internal Used by CurrencyRegistry only. Use Currency::of() everywhere else.
     */
    public static function fromRegistry(
        string $code,
        string $numericCode,
        string $name,
        int $scale,
        string $symbol,
    ): self {
        return new self($code, $numericCode, $name, $scale, $symbol);
    }

    /**
     * Return the ISO 4217 alphabetic code (e.g. "EUR").
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Return the ISO 4217 numeric code as a zero-padded string (e.g. "978").
     */
    public function getNumericCode(): string
    {
        return $this->numericCode;
    }

    /**
     * Return the full ISO 4217 currency name (e.g. "Euro").
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the number of decimal places for minor units.
     *
     * Examples: EUR → 2 (1 EUR = 100 cents), JPY → 0, KWD → 3.
     */
    public function getScale(): int
    {
        return $this->scale;
    }

    /**
     * Return the commonly used currency symbol (e.g. "€", "$", "¥").
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * Return true if this currency has the same ISO 4217 code as the other.
     */
    public function isEqualTo(self $other): bool
    {
        return $this->code === $other->code;
    }

    /**
     * Return the ISO 4217 alphabetic code.
     */
    public function __toString(): string
    {
        return $this->code;
    }
}
