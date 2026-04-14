# Coding Guidelines

Applies to the entire ez-php project — framework core, all modules, and the application template.

---

## Environment

- PHP **8.5**, Composer for dependency management
- All project based commands run **inside Docker** — never directly on the host

```
docker compose exec app <command>
```

Container name: `ez-php-app`, service name: `app`.

---

## Quality Suite

Run after every change:

```
docker compose exec app composer full
```

Executes in order:
1. `phpstan analyse` — static analysis, level 9, config: `phpstan.neon`
2. `php-cs-fixer fix` — auto-fixes style (`@PSR12` + `@PHP83Migration` + strict rules)
3. `phpunit` — all tests with coverage

Individual commands when needed:
```
composer analyse   # PHPStan only
composer cs        # CS Fixer only
composer test      # PHPUnit only
```

**PHPStan:** never suppress with `@phpstan-ignore-line` — always fix the root cause.

---

## Coding Standards

- `declare(strict_types=1)` at the top of every PHP file
- Typed properties, parameters, and return values — avoid `mixed`
- PHPDoc on every class and public method
- One responsibility per class — keep classes small and focused
- Constructor injection — no service locator pattern
- No global state unless intentional and documented

**Naming:**

| Thing | Convention |
|---|---|
| Classes / Interfaces | `PascalCase` |
| Methods / variables | `camelCase` |
| Constants | `UPPER_CASE` |
| Files | Match class name exactly |

**Principles:** SOLID · KISS · DRY · YAGNI

---

## Workflow & Behavior

- Write tests **before or alongside** production code (test-first)
- Read and understand the relevant code before making any changes
- Modify the minimal number of files necessary
- Keep implementations small — if it feels big, it likely belongs in a separate module
- No hidden magic — everything must be explicit and traceable
- No large abstractions without clear necessity
- No heavy dependencies — check if PHP stdlib suffices first
- Respect module boundaries — don't reach across packages
- Keep the framework core small — what belongs in a module stays there
- Document architectural reasoning for non-obvious design decisions
- Do not change public APIs unless necessary
- Prefer composition over inheritance — no premature abstractions

---

## New Modules & CLAUDE.md Files

### 1 — Required files

Every module under `modules/<name>/` must have:

| File | Purpose |
|---|---|
| `composer.json` | package definition, deps, autoload |
| `phpstan.neon` | static analysis config, level 9 |
| `phpunit.xml` | test suite config |
| `.php-cs-fixer.php` | code style config |
| `.gitignore` | ignore `vendor/`, `.env`, cache |
| `.github/workflows/ci.yml` | standalone CI pipeline |
| `README.md` | public documentation |
| `tests/TestCase.php` | base test case for the module |
| `start.sh` | convenience script: copy `.env`, bring up Docker, wait for services, exec shell |
| `CLAUDE.md` | see section 2 below |

### 2 — CLAUDE.md structure

Every module `CLAUDE.md` must follow this exact structure:

1. **Full content of `CODING_GUIDELINES.md`, verbatim** — copy it as-is, do not summarize or shorten
2. A `---` separator
3. `# Package: ez-php/<name>` (or `# Directory: <name>` for non-package directories)
4. Module-specific section covering:
   - Source structure — file tree with one-line description per file
   - Key classes and their responsibilities
   - Design decisions and constraints
   - Testing approach and infrastructure requirements (MySQL, Redis, etc.)
   - What does **not** belong in this module

### 3 — Docker scaffold

Run from the new module root (requires `"ez-php/docker": "^1.0"` in `require-dev`):

```
vendor/bin/docker-init
```

This copies `Dockerfile`, `docker-compose.yml`, `.env.example`, `start.sh`, and `docker/` into the module, replacing `{{MODULE_NAME}}` placeholders. Existing files are never overwritten.

After scaffolding:

1. Adapt `docker-compose.yml` — add or remove services (MySQL, Redis) as needed
2. Adapt `.env.example` — fill in connection defaults matching the services above
3. Assign a unique host port for each exposed service (see table below)

**Allocated host ports:**

| Package | `DB_HOST_PORT` (MySQL) | `REDIS_PORT` |
|---|---|---|
| root (`ez-php-project`) | 3306 | 6379 |
| `ez-php/framework` | 3307 | — |
| `ez-php/orm` | 3309 | — |
| `ez-php/cache` | — | 6380 |
| `ez-php/queue` | 3310 | 6381 |
| `ez-php/rate-limiter` | — | 6382 |
| **next free** | **3311** | **6383** |

Only set a port for services the module actually uses. Modules without external services need no port config.

### 4 — Monorepo scripts

`packages.sh` at the project root is the **central package registry**. Both `push_all.sh` and `update_all.sh` source it — the package list lives in exactly one place.

When adding a new module, add `"$ROOT/modules/<name>"` to the `PACKAGES` array in `packages.sh` in **alphabetical order** among the other `modules/*` entries (before `framework`, `ez-php`, and the root entry at the end).

---

# Package: ez-php/money

Currency and monetary arithmetic. Immutable `Money` and `Currency` value objects composing `ez-php/bignum` for all arithmetic. No raw bcmath or gmp calls in this package — all math goes through `BigDecimal`.

---

## Source Structure

```
src/
  Currency.php                    — ISO 4217 currency value object (code, scale, symbol)
  CurrencyRegistry.php            — Static registry; loads bundled data, caches instances
  Money.php                       — Immutable monetary value object (BigDecimal + Currency)
  Data/
    currencies.php                — Bundled ISO 4217 data array (~150 currencies)
  Exception/
    MoneyException.php            — Base exception (extends \RuntimeException)
    CurrencyMismatchException.php — Thrown on arithmetic between different currencies
    UnknownCurrencyException.php  — Thrown for unrecognised ISO 4217 codes
  Formatter/
    MoneyFormatter.php            — Interface: format(Money): string
    DecimalMoneyFormatter.php     — Plain decimal output, optional currency code suffix
    IntlMoneyFormatter.php        — Locale-aware output via ext-intl NumberFormatter
  Parser/
    MoneyParser.php               — Interface: parse(string, Currency|string): Money
    DecimalMoneyParser.php        — Parses plain decimal strings
    IntlMoneyParser.php           — Parses locale-formatted strings via ext-intl
tests/
  TestCase.php                    — Base test case (no framework coupling)
  CurrencyTest.php                — Currency factory, accessors, equality, unknown code
  CurrencyRegistryTest.php        — Registry get/all/has, caching, case-insensitivity
  MoneyTest.php                   — Full coverage of Money: factory, arithmetic, allocation, comparison
  Formatter/
    DecimalMoneyFormatterTest.php — Format with/without currency code, various scales
    IntlMoneyFormatterTest.php    — Locale-aware format (requires ext-intl)
  Parser/
    DecimalMoneyParserTest.php    — Parse valid/invalid decimal strings
    IntlMoneyParserTest.php       — Round-trip parse via IntlMoneyFormatter (requires ext-intl)
```

---

## Key Classes and Responsibilities

### Money (`src/Money.php`)

Final, immutable value object.

| Concern | Detail |
|---|---|
| Storage | `$amount: BigDecimal` at currency scale + `$currency: Currency` |
| Scale invariant | `amount` is always at `currency->getScale()` decimal places |
| Factory | `of(string\|int, Currency\|string, RoundingMode)` — rounds to scale on construction |
| Minor units | `ofMinorUnits(int, Currency\|string)` — amount = units / 10^scale |
| No raw math | All arithmetic delegates to `BigDecimal` methods |
| Allocation | `allocate(int[])` — splits by ratio without losing a minor unit |

`add()` and `subtract()` produce exact results (same scale → max scale = same scale). `multiply()` and `divide()` require an explicit `RoundingMode` because the result scale may differ.

### Currency (`src/Currency.php`)

Final, immutable ISO 4217 value object. Constructed only via `Currency::of()` (delegates to registry) or `Currency::fromRegistry()` (used by registry internally, `@internal`).

### CurrencyRegistry (`src/CurrencyRegistry.php`)

Pure static class. Loads `src/Data/currencies.php` once on first access, caches `Currency` instances by code. Case-insensitive lookup (`strtoupper` normalisation).

### Data format (`src/Data/currencies.php`)

Returns `array<string, array{0: string, 1: string, 2: int, 3: string}>`:
`'EUR' => ['978', 'Euro', 2, '€']` — [numericCode, name, scale, symbol]

---

## Design Decisions and Constraints

- **All amounts stored at currency scale** — the `BigDecimal::getUnscaledValue()` of a Money is directly the minor-unit integer. `toMinorUnits()` just wraps it in a `BigInteger` at zero cost.

- **No implicit rounding on add/subtract** — since both operands are at the same scale, the result is always exact. The `toScale()` call in `multiply()` and `dividedBy()` in `divide()` are the only places rounding occurs.

- **`divide()` requires explicit RoundingMode** — unlike `multiply()` which defaults to `HALF_UP`, `divide()` forces the caller to name the mode. Division is inherently imprecise and the choice matters more.

- **Allocation algorithm** — works entirely in minor units (integers) to avoid floating-point artifacts. Remainder < count(ratios) by construction, so the `while` loop always terminates quickly.

- **`Currency::fromRegistry()` is `@internal`** — it is the only way to construct a `Currency` without going through the registry. Named to make misuse obvious.

- **`IntlMoneyFormatter`/`IntlMoneyParser` check `extension_loaded('intl')`** — they throw `\RuntimeException` at construction time if ext-intl is absent. This gives a clear error rather than a fatal "class not found" at runtime.

- **No framework coupling** — no container, service providers, or framework imports anywhere in the source. `ez-php/bignum` is the only runtime dependency.

---

## Testing Approach

- No external infrastructure required (no database, no Redis)
- `IntlMoneyFormatterTest` and `IntlMoneyParserTest` carry `#[RequiresPhpExtension('intl')]` and skip automatically when not installed
- `IntlMoneyParserTest::testParseDeDE` round-trips through `IntlMoneyFormatter` to produce a locale string the parser is guaranteed to understand (avoids ICU version divergence)
- Allocation tests verify the invariant that `sum(allocate(ratios)) == original`

---

## What Does NOT Belong Here

| Concern | Where it belongs |
|---|---|
| Exchange-rate conversion | `ez-php/exchange` (future) |
| Tax / VAT calculation | Application layer |
| Payment gateway integration | Infrastructure / application layer |
| Arbitrary-precision primitives | `ez-php/bignum` |
| Locale number formatting unrelated to money | Application layer or `ext-intl` directly |
