# ez-php/money

Immutable `Money` and `Currency` value objects for the ez-php ecosystem.

- ISO 4217 currencies with minor-unit scale (bundled, no network calls)
- Arbitrary-precision arithmetic via `ez-php/bignum`
- Allocation without losing a minor unit
- Locale-aware formatting via PHP's `ext-intl`

## Requirements

- PHP 8.5+
- `ext-gmp` (required, via `ez-php/bignum`)
- `ext-intl` (optional, for `IntlMoneyFormatter` / `IntlMoneyParser`)

## Installation

```bash
composer require ez-php/money
```

## Usage

### Creating Money

```php
use EzPhp\Money\Money;

$price  = Money::of('19.99', 'EUR');          // from decimal string
$tax    = Money::ofMinorUnits(199, 'EUR');     // from cents → 1.99 EUR
$zero   = Money::zero('USD');

// JPY has no minor units (scale = 0)
$yen    = Money::of('1000', 'JPY');
```

### Arithmetic

```php
use EzPhp\BigNum\RoundingMode;

$total  = $price->add($tax);                  // 21.98 EUR
$half   = $price->divide(2, RoundingMode::HALF_UP);
$vat    = $price->multiply('0.19');           // 3.80 EUR (rounded HALF_UP)
```

### Allocation

```php
// Split 10.00 EUR three ways — no cent is lost
[$a, $b, $c] = Money::of('10.00', 'EUR')->allocate([1, 1, 1]);
// 3.34 EUR, 3.33 EUR, 3.33 EUR  (total = 10.00 EUR ✓)

// Weighted split
[$deposit, $remainder] = Money::of('100.00', 'EUR')->allocate([1, 3]);
// 25.00 EUR, 75.00 EUR
```

### Comparison

```php
$price->isGreaterThan($tax);   // true
$price->isEqualTo($tax);       // false
$price->compareTo($tax);       // 1
```

### Minor units

```php
$price->toMinorUnits()->toString();  // "1999"
```

### Currencies

```php
use EzPhp\Money\Currency;

$eur = Currency::of('EUR');
$eur->getCode();        // "EUR"
$eur->getNumericCode(); // "978"
$eur->getName();        // "Euro"
$eur->getScale();       // 2
$eur->getSymbol();      // "€"
```

### Formatting

```php
use EzPhp\Money\Formatter\DecimalMoneyFormatter;
use EzPhp\Money\Formatter\IntlMoneyFormatter;

$plain = new DecimalMoneyFormatter();
$plain->format($price);                        // "19.99"

$withCode = new DecimalMoneyFormatter(includeCurrencyCode: true);
$withCode->format($price);                     // "19.99 EUR"

$intl = new IntlMoneyFormatter('de_DE');        // requires ext-intl
$intl->format($price);                         // "19,99 €"
```

### Parsing

```php
use EzPhp\Money\Parser\DecimalMoneyParser;
use EzPhp\Money\Parser\IntlMoneyParser;

$parser = new DecimalMoneyParser();
$money  = $parser->parse('19.99', 'EUR');

$intlParser = new IntlMoneyParser('en_US');     // requires ext-intl
$money      = $intlParser->parse('$19.99', 'USD');
```

### Exception hierarchy

```
MoneyException (extends RuntimeException)
├── CurrencyMismatchException  — arithmetic between different currencies
└── UnknownCurrencyException   — unrecognised ISO 4217 code
```

## Currencies

The bundled registry covers ~150 ISO 4217 currencies. Lookups are case-insensitive and
cached after first access.

```php
use EzPhp\Money\CurrencyRegistry;

CurrencyRegistry::has('EUR');    // true
CurrencyRegistry::has('ZZZ');   // false
CurrencyRegistry::all();         // array<string, Currency>
```
