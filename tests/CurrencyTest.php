<?php

declare(strict_types=1);

namespace Tests;

use EzPhp\Money\Currency;
use EzPhp\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Currency::class)]
final class CurrencyTest extends TestCase
{
    public function testOfReturnsCorrectCurrency(): void
    {
        $eur = Currency::of('EUR');

        self::assertSame('EUR', $eur->getCode());
        self::assertSame('978', $eur->getNumericCode());
        self::assertSame('Euro', $eur->getName());
        self::assertSame(2, $eur->getScale());
        self::assertSame('€', $eur->getSymbol());
    }

    public function testOfIsCaseInsensitive(): void
    {
        $a = Currency::of('eur');
        $b = Currency::of('EUR');

        self::assertSame($a->getCode(), $b->getCode());
    }

    public function testOfReturnsSameInstanceForSameCode(): void
    {
        self::assertSame(Currency::of('USD'), Currency::of('USD'));
    }

    public function testOfThrowsForUnknownCode(): void
    {
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessage('Unknown currency code: "XYZ"');
        Currency::of('XYZ');
    }

    public function testZeroScaleCurrency(): void
    {
        $jpy = Currency::of('JPY');

        self::assertSame(0, $jpy->getScale());
        self::assertSame('392', $jpy->getNumericCode());
    }

    public function testThreeScaleCurrency(): void
    {
        $kwd = Currency::of('KWD');

        self::assertSame(3, $kwd->getScale());
        self::assertSame('414', $kwd->getNumericCode());
    }

    public function testFourScaleCurrency(): void
    {
        $clf = Currency::of('CLF');

        self::assertSame(4, $clf->getScale());
    }

    public function testIsEqualTo(): void
    {
        self::assertTrue(Currency::of('EUR')->isEqualTo(Currency::of('EUR')));
        self::assertFalse(Currency::of('EUR')->isEqualTo(Currency::of('USD')));
    }

    public function testToString(): void
    {
        self::assertSame('EUR', (string) Currency::of('EUR'));
    }
}
