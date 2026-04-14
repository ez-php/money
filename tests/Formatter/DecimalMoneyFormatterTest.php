<?php

declare(strict_types=1);

namespace Tests\Formatter;

use EzPhp\Money\Formatter\DecimalMoneyFormatter;
use EzPhp\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DecimalMoneyFormatter::class)]
final class DecimalMoneyFormatterTest extends TestCase
{
    public function testFormatAmountOnly(): void
    {
        $formatter = new DecimalMoneyFormatter();

        self::assertSame('10.50', $formatter->format(Money::of('10.50', 'EUR')));
    }

    public function testFormatWithCurrencyCode(): void
    {
        $formatter = new DecimalMoneyFormatter(includeCurrencyCode: true);

        self::assertSame('10.50 EUR', $formatter->format(Money::of('10.50', 'EUR')));
    }

    public function testFormatNegativeAmount(): void
    {
        $formatter = new DecimalMoneyFormatter(includeCurrencyCode: true);

        self::assertSame('-5.99 USD', $formatter->format(Money::of('-5.99', 'USD')));
    }

    public function testFormatZeroScaleCurrency(): void
    {
        $formatter = new DecimalMoneyFormatter(includeCurrencyCode: true);

        self::assertSame('1000 JPY', $formatter->format(Money::of('1000', 'JPY')));
    }

    public function testFormatThreeScaleCurrency(): void
    {
        $formatter = new DecimalMoneyFormatter();

        self::assertSame('1.234', $formatter->format(Money::of('1.234', 'KWD')));
    }
}
