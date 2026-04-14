<?php

declare(strict_types=1);

namespace Tests\Formatter;

use EzPhp\Money\Formatter\IntlMoneyFormatter;
use EzPhp\Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[CoversClass(IntlMoneyFormatter::class)]
#[RequiresPhpExtension('intl')]
final class IntlMoneyFormatterTest extends TestCase
{
    public function testFormatEnUs(): void
    {
        $formatter = new IntlMoneyFormatter('en_US');
        $result = $formatter->format(Money::of('1234.56', 'USD'));

        // NumberFormatter output varies by ICU version, but must contain the amount digits
        self::assertStringContainsString('1,234.56', $result);
    }

    public function testFormatDeDE(): void
    {
        $formatter = new IntlMoneyFormatter('de_DE');
        $result = $formatter->format(Money::of('1234.56', 'EUR'));

        // German locale uses period as thousands separator and comma as decimal
        self::assertStringContainsString('1.234,56', $result);
    }

    public function testFormatJpy(): void
    {
        $formatter = new IntlMoneyFormatter('ja_JP');
        $result = $formatter->format(Money::of('1000', 'JPY'));

        self::assertStringContainsString('1,000', $result);
    }
}
