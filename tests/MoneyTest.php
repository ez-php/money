<?php

declare(strict_types=1);

namespace Tests;

use EzPhp\BigNum\BigDecimal;
use EzPhp\BigNum\DivisionByZeroException;
use EzPhp\BigNum\RoundingMode;
use EzPhp\Money\Currency;
use EzPhp\Money\Exception\CurrencyMismatchException;
use EzPhp\Money\Exception\UnknownCurrencyException;
use EzPhp\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(CurrencyMismatchException::class)]
#[UsesClass(UnknownCurrencyException::class)]
final class MoneyTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Factory
    // -------------------------------------------------------------------------

    public function testOfFromString(): void
    {
        $money = Money::of('10.50', 'EUR');

        self::assertSame('10.50', $money->getAmount()->toString());
        self::assertSame('EUR', $money->getCurrency()->getCode());
    }

    public function testOfFromInt(): void
    {
        $money = Money::of(10, 'EUR');

        self::assertSame('10.00', $money->getAmount()->toString());
    }

    public function testOfScalesToCurrencyScale(): void
    {
        // EUR has scale 2; input with more decimals is rounded
        $money = Money::of('10.999', 'EUR', RoundingMode::HALF_UP);

        self::assertSame('11.00', $money->getAmount()->toString());
    }

    public function testOfScalesToCurrencyScaleDown(): void
    {
        $money = Money::of('10.994', 'EUR', RoundingMode::HALF_UP);

        self::assertSame('10.99', $money->getAmount()->toString());
    }

    public function testOfWithZeroScaleCurrency(): void
    {
        $jpy = Money::of('1000', 'JPY');

        self::assertSame('1000', $jpy->getAmount()->toString());
        self::assertSame(0, $jpy->getCurrency()->getScale());
    }

    public function testOfWithThreeScaleCurrency(): void
    {
        $kwd = Money::of('1.234', 'KWD');

        self::assertSame('1.234', $kwd->getAmount()->toString());
    }

    public function testOfAcceptsCurrencyObject(): void
    {
        $money = Money::of('5.00', Currency::of('USD'));

        self::assertSame('USD', $money->getCurrency()->getCode());
    }

    public function testOfThrowsForUnknownCurrency(): void
    {
        $this->expectException(UnknownCurrencyException::class);
        Money::of('1.00', 'ZZZ');
    }

    public function testOfMinorUnits(): void
    {
        $money = Money::ofMinorUnits(1050, 'EUR');

        self::assertSame('10.50', $money->getAmount()->toString());
    }

    public function testOfMinorUnitsJpy(): void
    {
        $jpy = Money::ofMinorUnits(1000, 'JPY');

        self::assertSame('1000', $jpy->getAmount()->toString());
    }

    public function testOfMinorUnitsNegative(): void
    {
        $money = Money::ofMinorUnits(-500, 'EUR');

        self::assertSame('-5.00', $money->getAmount()->toString());
    }

    public function testZero(): void
    {
        $money = Money::zero('EUR');

        self::assertSame('0.00', $money->getAmount()->toString());
        self::assertTrue($money->isZero());
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function testGetAmount(): void
    {
        $money = Money::of('12.34', 'EUR');

        self::assertSame('12.34', $money->getAmount()->toString());
    }

    public function testGetCurrency(): void
    {
        $money = Money::of('1.00', 'USD');

        self::assertSame('USD', $money->getCurrency()->getCode());
    }

    public function testToMinorUnits(): void
    {
        self::assertSame('1050', Money::of('10.50', 'EUR')->toMinorUnits()->toString());
        self::assertSame('1000', Money::of('1000', 'JPY')->toMinorUnits()->toString());
        self::assertSame('1234', Money::of('1.234', 'KWD')->toMinorUnits()->toString());
    }

    public function testToMinorUnitsNegative(): void
    {
        self::assertSame('-1050', Money::of('-10.50', 'EUR')->toMinorUnits()->toString());
    }

    // -------------------------------------------------------------------------
    // Arithmetic
    // -------------------------------------------------------------------------

    public function testAdd(): void
    {
        $a = Money::of('10.50', 'EUR');
        $b = Money::of('3.25', 'EUR');

        self::assertSame('13.75', $a->add($b)->getAmount()->toString());
    }

    public function testAddNegative(): void
    {
        $a = Money::of('5.00', 'EUR');
        $b = Money::of('-3.00', 'EUR');

        self::assertSame('2.00', $a->add($b)->getAmount()->toString());
    }

    public function testSubtract(): void
    {
        $a = Money::of('10.00', 'EUR');
        $b = Money::of('3.50', 'EUR');

        self::assertSame('6.50', $a->subtract($b)->getAmount()->toString());
    }

    public function testSubtractProducesNegative(): void
    {
        $a = Money::of('3.00', 'EUR');
        $b = Money::of('5.00', 'EUR');

        self::assertSame('-2.00', $a->subtract($b)->getAmount()->toString());
    }

    public function testAddThrowsOnCurrencyMismatch(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        Money::of('1.00', 'EUR')->add(Money::of('1.00', 'USD'));
    }

    public function testSubtractThrowsOnCurrencyMismatch(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        Money::of('1.00', 'EUR')->subtract(Money::of('1.00', 'USD'));
    }

    public function testMultiplyByInt(): void
    {
        $money = Money::of('10.00', 'EUR')->multiply(3);

        self::assertSame('30.00', $money->getAmount()->toString());
    }

    public function testMultiplyByDecimalString(): void
    {
        $money = Money::of('10.00', 'EUR')->multiply('1.5');

        self::assertSame('15.00', $money->getAmount()->toString());
    }

    public function testMultiplyRoundsToScale(): void
    {
        // 10.00 * 1.333 = 13.33 (HALF_UP)
        $money = Money::of('10.00', 'EUR')->multiply('1.333', RoundingMode::HALF_UP);

        self::assertSame('13.33', $money->getAmount()->toString());
    }

    public function testMultiplyByBigDecimal(): void
    {
        $money = Money::of('20.00', 'EUR')->multiply(BigDecimal::of('0.5'));

        self::assertSame('10.00', $money->getAmount()->toString());
    }

    public function testDivide(): void
    {
        $money = Money::of('10.00', 'EUR')->divide(4, RoundingMode::HALF_UP);

        self::assertSame('2.50', $money->getAmount()->toString());
    }

    public function testDivideRounds(): void
    {
        // 10.00 / 3 = 3.333... → rounds to 3.33 (HALF_UP)
        $money = Money::of('10.00', 'EUR')->divide(3, RoundingMode::HALF_UP);

        self::assertSame('3.33', $money->getAmount()->toString());
    }

    public function testDivideByZeroThrows(): void
    {
        $this->expectException(DivisionByZeroException::class);
        Money::of('10.00', 'EUR')->divide(0, RoundingMode::HALF_UP);
    }

    public function testAbs(): void
    {
        self::assertSame('5.00', Money::of('-5.00', 'EUR')->abs()->getAmount()->toString());
        self::assertSame('5.00', Money::of('5.00', 'EUR')->abs()->getAmount()->toString());
    }

    public function testNegate(): void
    {
        self::assertSame('-5.00', Money::of('5.00', 'EUR')->negate()->getAmount()->toString());
        self::assertSame('5.00', Money::of('-5.00', 'EUR')->negate()->getAmount()->toString());
        self::assertSame('0.00', Money::of('0.00', 'EUR')->negate()->getAmount()->toString());
    }

    // -------------------------------------------------------------------------
    // Immutability
    // -------------------------------------------------------------------------

    public function testImmutability(): void
    {
        $a = Money::of('10.00', 'EUR');
        $b = $a->add(Money::of('5.00', 'EUR'));

        self::assertSame('10.00', $a->getAmount()->toString());
        self::assertSame('15.00', $b->getAmount()->toString());
    }

    // -------------------------------------------------------------------------
    // Allocation
    // -------------------------------------------------------------------------

    public function testAllocateEvenSplit(): void
    {
        $parts = Money::of('10.00', 'EUR')->allocate([1, 1]);

        self::assertCount(2, $parts);
        self::assertSame('5.00', $parts[0]->getAmount()->toString());
        self::assertSame('5.00', $parts[1]->getAmount()->toString());
    }

    public function testAllocateUnevenSplit(): void
    {
        // 10.00 / 3 = [3.34, 3.33, 3.33]
        $parts = Money::of('10.00', 'EUR')->allocate([1, 1, 1]);

        self::assertCount(3, $parts);
        self::assertSame('3.34', $parts[0]->getAmount()->toString());
        self::assertSame('3.33', $parts[1]->getAmount()->toString());
        self::assertSame('3.33', $parts[2]->getAmount()->toString());

        // Verify total is preserved
        $total = array_reduce(
            $parts,
            fn (Money $carry, Money $part) => $carry->add($part),
            Money::zero('EUR'),
        );
        self::assertSame('10.00', $total->getAmount()->toString());
    }

    public function testAllocateWeightedRatios(): void
    {
        // 100.00 EUR split [1:3] → [25.00, 75.00]
        $parts = Money::of('100.00', 'EUR')->allocate([1, 3]);

        self::assertSame('25.00', $parts[0]->getAmount()->toString());
        self::assertSame('75.00', $parts[1]->getAmount()->toString());
    }

    public function testAllocatePreservesTotal(): void
    {
        $money = Money::of('0.05', 'EUR');
        $parts = $money->allocate([1, 1, 1]);

        $total = array_reduce(
            $parts,
            fn (Money $carry, Money $part) => $carry->add($part),
            Money::zero('EUR'),
        );
        self::assertTrue($money->isEqualTo($total));
    }

    public function testAllocateNegative(): void
    {
        $parts = Money::of('-10.00', 'EUR')->allocate([1, 1, 1]);

        self::assertSame('-3.34', $parts[0]->getAmount()->toString());
        self::assertSame('-3.33', $parts[1]->getAmount()->toString());
        self::assertSame('-3.33', $parts[2]->getAmount()->toString());
    }

    public function testAllocateZero(): void
    {
        $parts = Money::zero('EUR')->allocate([1, 1, 1]);

        foreach ($parts as $part) {
            self::assertTrue($part->isZero());
        }
    }

    public function testAllocateJpy(): void
    {
        // JPY has scale 0 — allocation works in whole yen
        $parts = Money::of('10', 'JPY')->allocate([1, 1, 1]);

        self::assertSame('4', $parts[0]->getAmount()->toString());
        self::assertSame('3', $parts[1]->getAmount()->toString());
        self::assertSame('3', $parts[2]->getAmount()->toString());
    }

    public function testAllocateEmptyRatiosThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::of('10.00', 'EUR')->allocate([]);
    }

    public function testAllocateNegativeRatioThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::of('10.00', 'EUR')->allocate([1, -1]);
    }

    public function testAllocateZeroSumRatiosThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::of('10.00', 'EUR')->allocate([0, 0]);
    }

    // -------------------------------------------------------------------------
    // Comparison
    // -------------------------------------------------------------------------

    public function testCompareTo(): void
    {
        $five = Money::of('5.00', 'EUR');
        $ten = Money::of('10.00', 'EUR');

        self::assertSame(-1, $five->compareTo($ten));
        self::assertSame(0, $five->compareTo(Money::of('5.00', 'EUR')));
        self::assertSame(1, $ten->compareTo($five));
    }

    public function testCompareToThrowsOnMismatch(): void
    {
        $this->expectException(CurrencyMismatchException::class);
        Money::of('1.00', 'EUR')->compareTo(Money::of('1.00', 'USD'));
    }

    public function testIsEqualTo(): void
    {
        self::assertTrue(Money::of('5.00', 'EUR')->isEqualTo(Money::of('5.00', 'EUR')));
        self::assertFalse(Money::of('5.00', 'EUR')->isEqualTo(Money::of('6.00', 'EUR')));
    }

    public function testIsLessThan(): void
    {
        self::assertTrue(Money::of('4.00', 'EUR')->isLessThan(Money::of('5.00', 'EUR')));
        self::assertFalse(Money::of('5.00', 'EUR')->isLessThan(Money::of('5.00', 'EUR')));
    }

    public function testIsGreaterThan(): void
    {
        self::assertTrue(Money::of('6.00', 'EUR')->isGreaterThan(Money::of('5.00', 'EUR')));
        self::assertFalse(Money::of('5.00', 'EUR')->isGreaterThan(Money::of('5.00', 'EUR')));
    }

    public function testIsLessThanOrEqualTo(): void
    {
        self::assertTrue(Money::of('5.00', 'EUR')->isLessThanOrEqualTo(Money::of('5.00', 'EUR')));
        self::assertTrue(Money::of('4.00', 'EUR')->isLessThanOrEqualTo(Money::of('5.00', 'EUR')));
        self::assertFalse(Money::of('6.00', 'EUR')->isLessThanOrEqualTo(Money::of('5.00', 'EUR')));
    }

    public function testIsGreaterThanOrEqualTo(): void
    {
        self::assertTrue(Money::of('5.00', 'EUR')->isGreaterThanOrEqualTo(Money::of('5.00', 'EUR')));
        self::assertTrue(Money::of('6.00', 'EUR')->isGreaterThanOrEqualTo(Money::of('5.00', 'EUR')));
        self::assertFalse(Money::of('4.00', 'EUR')->isGreaterThanOrEqualTo(Money::of('5.00', 'EUR')));
    }

    public function testIsZero(): void
    {
        self::assertTrue(Money::zero('EUR')->isZero());
        self::assertFalse(Money::of('0.01', 'EUR')->isZero());
    }

    public function testIsPositive(): void
    {
        self::assertTrue(Money::of('0.01', 'EUR')->isPositive());
        self::assertFalse(Money::zero('EUR')->isPositive());
        self::assertFalse(Money::of('-0.01', 'EUR')->isPositive());
    }

    public function testIsNegative(): void
    {
        self::assertTrue(Money::of('-0.01', 'EUR')->isNegative());
        self::assertFalse(Money::zero('EUR')->isNegative());
        self::assertFalse(Money::of('0.01', 'EUR')->isNegative());
    }

    // -------------------------------------------------------------------------
    // Conversion
    // -------------------------------------------------------------------------

    public function testToString(): void
    {
        self::assertSame('10.50 EUR', Money::of('10.50', 'EUR')->toString());
    }

    public function testStringCast(): void
    {
        self::assertSame('1000 JPY', (string) Money::of('1000', 'JPY'));
    }

    // -------------------------------------------------------------------------
    // Large amounts
    // -------------------------------------------------------------------------

    public function testLargeAmounts(): void
    {
        $a = Money::of('999999999999999999.99', 'EUR');
        $b = Money::of('0.01', 'EUR');

        self::assertSame('1000000000000000000.00', $a->add($b)->getAmount()->toString());
    }

    // -------------------------------------------------------------------------
    // Data providers
    // -------------------------------------------------------------------------

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function arithmeticProvider(): array
    {
        return [
            'add positive' => ['10.00', 'EUR', '5.00', '15.00'],
            'add negative' => ['-10.00', 'EUR', '5.00', '-5.00'],
            'add negative b' => ['10.00', 'EUR', '-3.00', '7.00'],
        ];
    }

    #[DataProvider('arithmeticProvider')]
    public function testArithmeticProvider(
        string $a,
        string $currency,
        string $b,
        string $expected,
    ): void {
        $result = Money::of($a, $currency)->add(Money::of($b, $currency));

        self::assertSame($expected, $result->getAmount()->toString());
    }
}
