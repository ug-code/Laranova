# Laranova — Agent Guide

## Package type

This is a **Laravel package** (`type: library`), not a Laravel app. Consumers install it via `composer require --dev laranova/laranova`. It has no artisan commands, no migrations, no build step.

## Namespace & autoloading

```
Laranova\  →  src/
```

Service provider auto-discovered via `composer.json` `extra.laravel.providers`, no manual `config/app.php` registration needed.

## Routes

All routes are group-prefixed `/laranova`, `web` middleware, named under `laranova.*`:

| Method | URI | Action |
|--------|-----|--------|
| GET | `/laranova/` | `index` — returns Blade SPA view |
| POST | `/laranova/send` | `send` — validates, parses faker tags, fires HTTP request, returns JSON |
| GET | `/laranova/history` | `history` — returns session-based history as JSON |
| DELETE | `/laranova/history` | `clearHistory` — clears session history |

**Local-only guard** — `LaranovaServiceProvider::boot()` returns early if `!$this->app->environment('local')`.

## Architecture

```
src/
├── LaranovaServiceProvider.php    # Registers routes, views, config, container bindings
├── Facades/Laranova.php           # Facade → ApiClient
├── Http/Controllers/LaranovaController.php  # readonly class, typed properties
└── Services/
    ├── ScriptParser.php           # Regex engine for {{ @faker:type }} tags
    └── ApiClient.php              # Laravel Http::send() wrapper, measures duration
```

### ScriptParser — faker tag engine

Pattern: `/\\{\\{\\s*@faker:(\\w+)\\s*\\}\\}/` — captures type, maps to `Faker\Generator` method via `FAKER_MAP`.

Supported types (~25): `name`, `firstName`, `lastName`, `email`, `safeEmail`, `phone`, `phoneNumber`, `address`, `streetAddress`, `city`, `country`, `postcode`, `text`, `sentence`, `paragraph`, `number`, `randomDigit`, `int`, `uuid`, `url`, `date`, `dateTime`, `company`, `boolean`, `word`, `title`.

Two public methods:
- `parse(string): string` — single string replacement
- `parseArray(array): array` — recursive replacement on all string values

Unknown types fall back to `$faker->word()`. Early return if `@faker:` not in string.

### ApiClient

Uses `Http::withHeaders()->withOptions()->send()`. SSL verification disabled (`verify: false`), redirects followed. Catches `ConnectionException` → returns `error: true` response array.

## Container bindings

`register()` method binds singletons:

| Abstract | Concrete construction |
|----------|----------------------|
| `ApiClient` | `new ApiClient(timeout, connectTimeout)` from `config('laranova.http.*')` |
| `ScriptParser` | `new ScriptParser(locale)` from `config('laranova.faker.locale')` |

## Config

Publish: `php artisan vendor:publish --tag=laranova-config`

Env overrides:
- `LARANOVA_FAKER_LOCALE` (default `en_US`)
- `LARANOVA_HTTP_TIMEOUT` (default `30`)
- `LARANOVA_HTTP_CONNECT_TIMEOUT` (default `10`)
- `LARANOVA_HISTORY_MAX` (default `100`)

## Views

`resources/views/laranova.blade.php` — single-file SPA. Rendered via `view('laranova::laranova')`.

Publish: `php artisan vendor:publish --tag=laranova-views`

Frontend dependencies loaded from CDN (no npm):
- **Tailwind CSS** — `cdn.tailwindcss.com`
- **Alpine.js 3.x** — `cdn.jsdelivr.net/npm/alpinejs@3.x.x`

Three-panel layout: left (history sidebar), center (request builder), right (response panel). CSRF token sent via `X-CSRF-TOKEN` header.

## PHP requirements & style

- PHP 8.2+ (typed properties, `readonly` class, promoted constructor properties)
- No code generation, no migrations, no queue jobs
- No test infrastructure yet (phpunit in require-dev but no phpunit.xml or test files)

## Key quirks

- **History is session-backed** — ephemeral, lost on session clear or server restart. No database persistence.
- **History only stores method + URL** on click-to-restore (not full request config).
- **Body field only visible** for POST/PUT/PATCH (Alpine `x-show`).
- **Response headers are normalized** — single-value arrays flattened to string by `ApiClient::normalizeHeaders()`.
- **Pre-scripts field** — faker tags are parsed but the output is discarded; only side effects of faker generation (if any) matter.
- **No SPA routing** — the page is a single Blade view, all interactivity is Alpine.js on one `x-data` scope.
- **CSRF required** for the `/laranova/send` POST endpoint (web middleware group).
