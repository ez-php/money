<?php

declare(strict_types=1);

namespace EzPhp\Money;

use EzPhp\BigNum\BigDecimal;
use EzPhp\BigNum\BigInteger;
use EzPhp\BigNum\RoundingMode;
use EzPhp\Money\Exception\CurrencyMismatchException;

/**
 * Immutable monetary value object.
 *
 * Wraps a BigDecimal amount (always at the currency's minor-unit scale) and a Currency.
 * All amounts are stored with the exact number of decimal places defined by the currency
 * (e.g. 2 for EUR, 0 for JPY, 3 for KWD).
 *
 * Arithmetic between different currencies always throws CurrencyMismatchException.
 * Since `amount` is stored at currency scale and add/subtract preserve that scale,
 * rounding only occurs in multiply() and divide() — both accept an explicit RoundingMode.
 *
 * @see Currency for ISO 4217 currency definitions
 * @see \EzPhp\BigNum\RoundingMode for available rounding strategies
 */
final class Money implements \Stringable
{
    private function __construct(
        private readonly BigDecimal $amount,
        private readonly Currency $currency,
    ) {
    }

    // -------------------------------------------------------------------------
    // Factory methods
    // -------------------------------------------------------------------------

    /**
     * Create a Money from a decimal amount and a currency.
     *
     * The amount is scaled to the currency's minor-unit precision. If the given
     * value has more decimal places than the currency supports, it is rounded
     * using the supplied rounding mode (default: HALF_UP). Pass an exact string
     * (e.g. "10.50") to avoid any rounding.
     *
     * @throws Exception\UnknownCurrencyException if $currency is an unrecognised code
     */
    public static function of(
        string|int $amount,
        Currency|string $currency,
        RoundingMode $roundingMode = RoundingMode::HALF_UP,
    ): self {
        $currency = self::resolveCurrency($currency);
        $decimal = BigDecimal::of($amount);
        $scaled = $decimal->toScale($currency->getScale(), $roundingMode);

        return new self($scaled, $currency);
    }

    /**
     * Create a Money from an integer amount in minor units (e.g. cents for EUR).
     *
     * Example: Money::ofMinorUnits(1050, 'EUR') → 10.50 EUR
     *
     * @throws Exception\UnknownCurrencyException if $currency is an unrecognised code
     */
    public static function ofMinorUnits(int $units, Currency|string $currency): self
    {
        $currency = self::resolveCurrency($currency);

        return new self(
            BigDecimal::ofUnscaledValue((string) $units, $currency->getScale()),
            $currency,
        );
    }

    /**
     * Create a zero Money for the given currency.
     *
     * @throws Exception\UnknownCurrencyException if $currency is an unrecognised code
     */
    public static function zero(Currency|string $currency): self
    {
        $currency = self::resolveCurrency($currency);

        return new self(
            BigDecimal::ofUnscaledValue('0', $currency->getScale()),
            $currency,
        );
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Return the decimal amount, always at the currency's minor-unit scale.
     */
    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    /**
     * Return the currency.
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Return the amount expressed as an integer in minor units.
     *
     * Example: 10.50 EUR → BigInteger(1050), 1000 JPY → BigInteger(1000)
     */
    public function toMinorUnits(): BigInteger
    {
        // amount is stored at currency scale, so unscaledValue IS the minor-unit integer
        return BigInteger::of($this->amount->getUnscaledValue());
    }

    // -------------------------------------------------------------------------
    // Arithmetic
    // -------------------------------------------------------------------------

    /**
     * Add another Money value. Both must use the same currency.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);

        // Both amounts are at the same currency scale; BigDecimal::add returns max(scale, scale)
        // = currency scale. No explicit toScale needed.
        return new self($this->amount->add($other->amount), $this->currency);
    }

    /**
     * Subtract another Money value. Both must use the same currency.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount->subtract($other->amount), $this->currency);
    }

    /**
     * Multiply by a scalar factor and round to the currency's scale.
     *
     * When $multiplier is an integer, no rounding occurs because the result scale
     * equals the currency scale. Rounding only occurs when $multiplier is a decimal.
     */
    public function multiply(
        int|string|BigDecimal $multiplier,
        RoundingMode $roundingMode = RoundingMode::HALF_UP,
    ): self {
        $factor = $multiplier instanceof BigDecimal ? $multiplier : BigDecimal::of($multiplier);
        $product = $this->amount->multiply($factor);
        $scaled = $product->toScale($this->currency->getScale(), $roundingMode);

        return new self($scaled, $this->currency);
    }

    /**
     * Divide by a scalar and round to the currency's scale.
     *
     * An explicit RoundingMode is required because division typically produces
     * an infinite-precision result that must be rounded to the currency's scale.
     *
     * @throws \EzPhp\BigNum\DivisionByZeroException if the divisor is zero
     */
    public function divide(int|string|BigDecimal $divisor, RoundingMode $roundingMode): self
    {
        $d = $divisor instanceof BigDecimal ? $divisor : BigDecimal::of($divisor);
        $result = $this->amount->dividedBy($d, $this->currency->getScale(), $roundingMode);

        return new self($result, $this->currency);
    }

    /**
     * Return the absolute value.
     */
    public function abs(): self
    {
        return new self($this->amount->abs(), $this->currency);
    }

    /**
     * Return the negated value.
     */
    public function negate(): self
    {
        return new self($this->amount->negate(), $this->currency);
    }

    // -------------------------------------------------------------------------
    // Allocation
    // -------------------------------------------------------------------------

    /**
     * Split this Money into parts according to the given ratios without losing a minor unit.
     *
     * Algorithm:
     *   1. Convert to minor units (integer).
     *   2. Compute floor(|totalUnits| × ratios[i] / sum(ratios)) for each ratio.
     *   3. Distribute any remaining minor units one at a time to the first allocations.
     *
     * Example: Money::of('10.00', 'EUR')->allocate([1, 1, 1])
     *   → [3.34 EUR, 3.33 EUR, 3.33 EUR]  (total = 10.00 EUR ✓)
     *
     * @param int[] $ratios Non-negative integers that sum to a positive value
     * @return Money[]
     *
     * @throws \InvalidArgumentException if ratios is empty, contains negatives, or sums to zero
     */
    public function allocate(array $ratios): array
    {
        if ($ratios === []) {
            throw new \InvalidArgumentException('Ratios array must not be empty');
        }

        $totalRatio = 0;

        foreach ($ratios as $ratio) {
            if ($ratio < 0) {
                throw new \InvalidArgumentException('Each ratio must be a non-negative integer, got ' . $ratio);
            }

            $totalRatio += $ratio;
        }

        if ($totalRatio === 0) {
            throw new \InvalidArgumentException('Ratios must sum to a positive value');
        }

        $totalUnits = $this->toMinorUnits();
        $isNegative = $totalUnits->isNegative();
        $absUnits = $totalUnits->abs();

        /** @var Money[] $allocations */
        $allocations = [];
        $sumAllocated = BigInteger::zero();

        foreach ($ratios as $ratio) {
            $part = $absUnits->multiply($ratio)->divide($totalRatio);
            $signed = $isNegative ? $part->negate() : $part;
            $allocations[] = self::fromMinorUnitsBigInteger($signed, $this->currency);
            $sumAllocated = $sumAllocated->add($part);
        }

        // remainder = |totalUnits| − sum of floor-allocated absolute values
        $remainder = $absUnits->subtract($sumAllocated);
        $unitDelta = self::ofMinorUnits($isNegative ? -1 : 1, $this->currency);
        $i = 0;

        while ($remainder->isPositive()) {
            $current = $allocations[$i] ?? throw new \LogicException('Allocation index out of bounds — this is a bug');
            $allocations[$i] = $current->add($unitDelta);
            $remainder = $remainder->subtract(BigInteger::one());
            $i++;
        }

        return $allocations;
    }

    // -------------------------------------------------------------------------
    // Comparison
    // -------------------------------------------------------------------------

    /**
     * Compare to another Money. Returns -1, 0, or 1.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function compareTo(Money $other): int
    {
        $this->assertSameCurrency($other);

        return $this->amount->compareTo($other->amount);
    }

    /**
     * Return true if this value equals the other (same amount and currency).
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function isEqualTo(Money $other): bool
    {
        return $this->compareTo($other) === 0;
    }

    /**
     * Return true if this value is less than the other.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function isLessThan(Money $other): bool
    {
        return $this->compareTo($other) < 0;
    }

    /**
     * Return true if this value is greater than the other.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function isGreaterThan(Money $other): bool
    {
        return $this->compareTo($other) > 0;
    }

    /**
     * Return true if this value is less than or equal to the other.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function isLessThanOrEqualTo(Money $other): bool
    {
        return $this->compareTo($other) <= 0;
    }

    /**
     * Return true if this value is greater than or equal to the other.
     *
     * @throws CurrencyMismatchException if currencies differ
     */
    public function isGreaterThanOrEqualTo(Money $other): bool
    {
        return $this->compareTo($other) >= 0;
    }

    /**
     * Return true if this value is zero.
     */
    public function isZero(): bool
    {
        return $this->amount->isZero();
    }

    /**
     * Return true if this value is strictly positive.
     */
    public function isPositive(): bool
    {
        return $this->amount->isPositive();
    }

    /**
     * Return true if this value is strictly negative.
     */
    public function isNegative(): bool
    {
        return $this->amount->isNegative();
    }

    // -------------------------------------------------------------------------
    // Conversion
    // -------------------------------------------------------------------------

    /**
     * Return a human-readable string: "{amount} {currencyCode}" (e.g. "10.50 EUR").
     */
    public function toString(): string
    {
        return $this->amount->toString() . ' ' . $this->currency->getCode();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    /**
     * Create a Money from a BigInteger representing minor units.
     */
    private static function fromMinorUnitsBigInteger(BigInteger $units, Currency $currency): self
    {
        return new self(
            BigDecimal::ofUnscaledValue($units->toString(), $currency->getScale()),
            $currency,
        );
    }

    /**
     * Resolve a Currency|string to a Currency instance.
     */
    private static function resolveCurrency(Currency|string $currency): Currency
    {
        if ($currency instanceof Currency) {
            return $currency;
        }

        return Currency::of($currency);
    }

    /**
     * Assert that the other Money has the same currency as this one.
     *
     * @throws CurrencyMismatchException
     */
    private function assertSameCurrency(Money $other): void
    {
        if (!$this->currency->isEqualTo($other->currency)) {
            throw new CurrencyMismatchException(
                $this->currency->getCode(),
                $other->currency->getCode(),
            );
        }
    }
}
