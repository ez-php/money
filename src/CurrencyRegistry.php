<?php

declare(strict_types=1);

namespace EzPhp\Money;

use EzPhp\Money\Exception\UnknownCurrencyException;

/**
 * Registry of ISO 4217 currencies backed by a bundled PHP data file.
 *
 * All lookups are cached in memory after the first access. The registry is
 * intentionally non-instantiable — use the static API directly.
 */
final class CurrencyRegistry
{
    /** @var array<string, array{0: string, 1: string, 2: int, 3: string}>|null */
    private static ?array $data = null;

    /** @var array<string, Currency> */
    private static array $instances = [];

    private function __construct()
    {
    }

    /**
     * Look up a currency by its ISO 4217 alphabetic code.
     *
     * The lookup is case-insensitive ("eur" and "EUR" both work).
     *
     * @throws UnknownCurrencyException if the code is not recognised
     */
    public static function get(string $code): Currency
    {
        $code = \strtoupper(\trim($code));

        if (isset(self::$instances[$code])) {
            return self::$instances[$code];
        }

        $data = self::loadData();

        if (!isset($data[$code])) {
            throw new UnknownCurrencyException($code);
        }

        $entry = $data[$code];
        $currency = Currency::fromRegistry($code, $entry[0], $entry[1], $entry[2], $entry[3]);
        self::$instances[$code] = $currency;

        return $currency;
    }

    /**
     * Return all registered currencies, keyed by ISO 4217 code.
     *
     * @return array<string, Currency>
     */
    public static function all(): array
    {
        $data = self::loadData();
        $result = [];

        foreach (\array_keys($data) as $code) {
            $result[$code] = self::get($code);
        }

        return $result;
    }

    /**
     * Return true if the given ISO 4217 code is known.
     */
    public static function has(string $code): bool
    {
        return isset(self::loadData()[\strtoupper(\trim($code))]);
    }

    /**
     * Load and cache the bundled ISO 4217 data file.
     *
     * @return array<string, array{0: string, 1: string, 2: int, 3: string}>
     */
    private static function loadData(): array
    {
        if (self::$data === null) {
            /** @var array<string, array{0: string, 1: string, 2: int, 3: string}> $loaded */
            $loaded = require __DIR__ . '/Data/currencies.php';
            self::$data = $loaded;
        }

        return self::$data;
    }
}
