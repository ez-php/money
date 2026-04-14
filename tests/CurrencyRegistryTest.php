<?php

declare(strict_types=1);

namespace Tests;

use EzPhp\Money\Currency;
use EzPhp\Money\CurrencyRegistry;
use EzPhp\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(CurrencyRegistry::class)]
#[UsesClass(Currency::class)]
#[UsesClass(UnknownCurrencyException::class)]
final class CurrencyRegistryTest extends TestCase
{
    public function testGetReturnsKnownCurrency(): void
    {
        $currency = CurrencyRegistry::get('EUR');

        self::assertSame('EUR', $currency->getCode());
    }

    public function testGetIsCaseInsensitive(): void
    {
        $a = CurrencyRegistry::get('usd');
        $b = CurrencyRegistry::get('USD');

        self::assertSame($a->getCode(), $b->getCode());
    }

    public function testGetReturnsCachedInstance(): void
    {
        self::assertSame(CurrencyRegistry::get('GBP'), CurrencyRegistry::get('GBP'));
    }

    public function testGetThrowsForUnknownCode(): void
    {
        $this->expectException(UnknownCurrencyException::class);
        CurrencyRegistry::get('ZZZ');
    }

    public function testHasReturnsTrueForKnownCode(): void
    {
        self::assertTrue(CurrencyRegistry::has('EUR'));
        self::assertTrue(CurrencyRegistry::has('jpy'));
    }

    public function testHasReturnsFalseForUnknownCode(): void
    {
        self::assertFalse(CurrencyRegistry::has('ZZZ'));
    }

    public function testAllReturnsNonEmptyArray(): void
    {
        $all = CurrencyRegistry::all();

        self::assertNotEmpty($all);
        self::assertArrayHasKey('EUR', $all);
        self::assertArrayHasKey('USD', $all);
        self::assertArrayHasKey('JPY', $all);
        self::assertContainsOnlyInstancesOf(Currency::class, $all);
    }
}
