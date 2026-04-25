<?php

declare(strict_types=1);

namespace EzPhp\Money\Parser;

use EzPhp\Money\Currency;
use EzPhp\Money\Money;

/**
 * Parses a locale-formatted money string using PHP's intl extension.
 *
 * Requires ext-intl. The parser uses NumberFormatter::parseCurrency() which
 * detects the currency from the formatted string (e.g. "€ 10,50" → EUR).
 * The $currency parameter serves as a fallback when the string contains no
 * recognisable currency symbol.
 *
 * @requires ext-intl
 */
final class IntlMoneyParser implements MoneyParser
{
    private readonly \NumberFormatter $formatter;

    /**
     * @param string $locale A BCP 47 locale string, e.g. "en_US", "de_DE"
     *
     * @throws \RuntimeException if ext-intl is not installed
     */
    public function __construct(string $locale)
    {
        if (!\extension_loaded('intl')) {
            throw new \RuntimeException(
                'The intl extension is required to use IntlMoneyParser. '
                . 'Install ext-intl or use DecimalMoneyParser instead.',
            );
        }

        $this->formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
    }

    /**
     * Parse a locale-formatted money string.
     *
     * The currency detected by the formatter takes precedence over the
     * $currency parameter. If no currency can be detected from the string,
     * the supplied $currency is used.
     *
     * @throws \InvalidArgumentException if the string cannot be parsed
     * @throws \EzPhp\Money\Exception\UnknownCurrencyException if the resolved currency code is unrecognised
     */
    public function parse(string $value, Currency|string $currency): Money
    {
        $detectedCode = '';
        $amount = $this->formatter->parseCurrency($value, $detectedCode);

        if ($amount === false) {
            throw new \InvalidArgumentException(
                \sprintf('IntlMoneyParser could not parse "%s": %s', $value, $this->formatter->getErrorMessage()),
            );
        }

        $resolvedCurrency = $detectedCode !== '' ? $detectedCode : $currency;

        return Money::of(\sprintf('%.14F', $amount), $resolvedCurrency);
    }
}
