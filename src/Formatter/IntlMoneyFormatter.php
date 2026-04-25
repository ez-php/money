<?php

declare(strict_types=1);

namespace EzPhp\Money\Formatter;

use EzPhp\Money\Money;

/**
 * Formats a Money value using PHP's intl extension for locale-aware output.
 *
 * Requires ext-intl. Example output for locale "de_DE": "10.234,56 €".
 *
 * @requires ext-intl
 */
final class IntlMoneyFormatter implements MoneyFormatter
{
    private readonly \NumberFormatter $formatter;

    /**
     * @param string $locale A BCP 47 locale string, e.g. "en_US", "de_DE", "fr_FR"
     *
     * @throws \RuntimeException if ext-intl is not installed
     */
    public function __construct(string $locale)
    {
        if (!\extension_loaded('intl')) {
            throw new \RuntimeException(
                'The intl extension is required to use IntlMoneyFormatter. '
                . 'Install ext-intl or use DecimalMoneyFormatter instead.',
            );
        }

        $this->formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
    }

    /**
     * Format the given Money value using locale-aware currency formatting.
     *
     * @throws \RuntimeException if formatting fails
     */
    public function format(Money $money): string
    {
        // Use the decimal string as a float for formatting. For extreme values
        // this may lose precision, but NumberFormatter works with floats by design.
        $result = $this->formatter->formatCurrency(
            $money->getAmount()->toFloat(),
            $money->getCurrency()->getCode(),
        );

        if ($result === false) {
            throw new \RuntimeException(
                'IntlMoneyFormatter failed: ' . $this->formatter->getErrorMessage(),
            );
        }

        return $result;
    }
}
