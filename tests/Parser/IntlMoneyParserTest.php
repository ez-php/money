<?php

declare(strict_types=1);

namespace Tests\Parser;

use EzPhp\Money\Parser\IntlMoneyParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[CoversClass(IntlMoneyParser::class)]
#[RequiresPhpExtension('intl')]
final class IntlMoneyParserTest extends TestCase
{
    public function testParseEnUs(): void
    {
        $parser = new IntlMoneyParser('en_US');
        $money = $parser->parse('$10.50', 'USD');

        self::assertSame('USD', $money->getCurrency()->getCode());
        self::assertSame('10.50', $money->getAmount()->toString());
    }

    public function testParseDeDE(): void
    {
        // Use the formatter to produce a string the parser is guaranteed to understand
        $formatter = new \EzPhp\Money\Formatter\IntlMoneyFormatter('de_DE');
        $original = \EzPhp\Money\Money::of('10.50', 'EUR');
        $formatted = $formatter->format($original);

        $parser = new IntlMoneyParser('de_DE');
        $money = $parser->parse($formatted, 'EUR');

        self::assertSame('EUR', $money->getCurrency()->getCode());
        self::assertSame('10.50', $money->getAmount()->toString());
    }

    public function testParseInvalidStringThrows(): void
    {
        $parser = new IntlMoneyParser('en_US');

        $this->expectException(\InvalidArgumentException::class);
        $parser->parse('not-money', 'USD');
    }
}
