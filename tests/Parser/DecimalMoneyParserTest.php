<?php

declare(strict_types=1);

namespace Tests\Parser;

use EzPhp\Money\Currency;
use EzPhp\Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\TestCase;

#[CoversClass(DecimalMoneyParser::class)]
#[UsesClass(Currency::class)]
final class DecimalMoneyParserTest extends TestCase
{
    private DecimalMoneyParser $parser;

    protected function setUp(): void
    {
        $this->parser = new DecimalMoneyParser();
    }

    public function testParseDecimalString(): void
    {
        $money = $this->parser->parse('10.50', 'EUR');

        self::assertSame('10.50', $money->getAmount()->toString());
        self::assertSame('EUR', $money->getCurrency()->getCode());
    }

    public function testParseInteger(): void
    {
        $money = $this->parser->parse('100', 'JPY');

        self::assertSame('100', $money->getAmount()->toString());
    }

    public function testParseNegativeAmount(): void
    {
        $money = $this->parser->parse('-5.99', 'USD');

        self::assertSame('-5.99', $money->getAmount()->toString());
    }

    public function testParseWithLeadingAndTrailingWhitespace(): void
    {
        $money = $this->parser->parse('  10.00  ', 'EUR');

        self::assertSame('10.00', $money->getAmount()->toString());
    }

    public function testParseAcceptsCurrencyObject(): void
    {
        $money = $this->parser->parse('1.00', Currency::of('GBP'));

        self::assertSame('GBP', $money->getCurrency()->getCode());
    }

    public function testParseInvalidStringThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->parse('not-a-number', 'EUR');
    }

    public function testParseSymbolStringThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->parse('€10.00', 'EUR');
    }
}
