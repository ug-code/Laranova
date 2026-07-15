# Laranova — Agent Guide

## Package type

This is a **Laravel package** (`type: library`), not a Laravel app. Consumers install it via `composer require --dev ug-code/laranova`. It has no artisan commands, no migrations, no build step.

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
| POST | `/laranova/resolve` | `resolve` — validates, parses faker tags, returns resolved JSON |
| GET | `/laranova/history` | `history` — returns session-based history as JSON |
| POST | `/laranova/history` | `storeHistory` — stores a request in session history |
| DELETE | `/laranova/history` | `clearHistory` — clears session history |
| GET | `/laranova/routes` | `routes` — returns scanned application routes as JSON |

**Local-only guard** — `LaranovaServiceProvider::boot()` returns early if `!$this->app->environment('local')`.

## Architecture

```
src/
├── LaranovaServiceProvider.php           # Registers routes, views, config, container bindings
├── Facades/Laranova.php                  # Facade → ApiClient
├── Http/Controllers/
│   └── LaranovaController.php            # 6 actions: index, resolve, routes, history, storeHistory, clearHistory
└── Services/
    ├── ScriptParser.php                  # Regex engine for {{ @faker:type }} tags
    ├── ApiClient.php                     # Laravel Http::send() wrapper, measures duration
    └── RouteScanner.php                  # Scans application routes, extracts methods/uri/rules/middleware
```

### ScriptParser — faker tag engine

Pattern: `/\{\{\s*@faker:(\w+)\s*\}\}/` — captures type, maps to `Faker\Generator` method via `FAKER_MAP`.

Two public methods:
- `parse(string): string` — single string replacement
- `parseArray(array): array` — recursive replacement on all string values

Unknown types fall back to `$faker->word()`. Early return if `@faker:` not in string.

### ApiClient

Uses `Http::withHeaders()->withOptions()->send()`. SSL verification disabled (`verify: false`), redirects followed. Catches `ConnectionException` → returns `error: true` response array.

### RouteScanner

Scans all registered Laravel routes via `Route::getRoutes()->getRoutes()`. Filters by `exclude_prefixes` config, skips closures and serialized closures. For each route extracts:
- `methods` — HTTP methods (HEAD filtered out)
- `uri`, `name`, `controller`, `action`
- `middleware` — route middleware stack
- `rules` — validation rules extracted via reflection on FormRequest parameters
- `pathParams` — URI parameters extracted via regex
- `has_file` — whether any rule references `file` or `image`

## Container bindings

`register()` method binds singletons:

| Abstract | Concrete construction |
|----------|----------------------|
| `ApiClient` | `new ApiClient(timeout, connectTimeout)` from `config('laranova.http.*')` |
| `ScriptParser` | `new ScriptParser(locale)` from `config('laranova.faker.locale')` |
| `RouteScanner` | `new RouteScanner()` (no-arg) |

## Config

Publish: `php artisan vendor:publish --tag=laranova-config`

| Key | Default | Env override |
|-----|---------|-------------|
| `default_headers` | `Content-Type: application/json`, `Accept: application/json` | — |
| `auto_content_type` | `true` | — |
| `faker.locale` | `en_US` | `LARANOVA_FAKER_LOCALE` |
| `http.timeout` | `30` | `LARANOVA_HTTP_TIMEOUT` |
| `http.connect_timeout` | `10` | `LARANOVA_HTTP_CONNECT_TIMEOUT` |
| `history.max_items` | `100` | `LARANOVA_HISTORY_MAX` |
| `routes.exclude_prefixes` | `laranova`, `request-docs`, `_debugbar`, `_ignition`, `telescope`, `sanctum` | — |
| `security.type` | `bearer` | `LARANOVA_SECURITY_TYPE` |
| `security.name` | `api_key` | `LARANOVA_SECURITY_NAME` |
| `security.position` | `header` | `LARANOVA_SECURITY_POSITION` |
| `default_variables` | `baseUrl`, `bearerToken` | `LARANOVA_DEFAULT_BASE_URL` |

## Views

```
resources/views/
├── laranova.blade.php                    # Main SPA shell (x-data="laranova()"), loads Alpine + Tailwind CDN
├── components/
│   ├── left-sidebar.blade.php            # Route tree, history, group/sort controls
│   ├── center-panel.blade.php            # Endpoint input, method selector, tabs, send button
│   ├── right-panel.blade.php             # Response viewer (status, headers, body, timing)
│   └── settings-modal.blade.php          # Config modal (variables, clear data)
└── partials/
    ├── tab-route-info.blade.php          # Route metadata (controller, middleware, rules)
    ├── tab-headers.blade.php             # Key-value header editor
    ├── tab-query-params.blade.php        # Key-value query parameter editor
    ├── tab-body.blade.php                # JSON body editor
    └── tab-pre-scripts.blade.php         # Pre-request script editor (Postman compat)
```

Publish: `php artisan vendor:publish --tag=laranova-views`

Frontend dependencies loaded from CDN (no npm):
- **Tailwind CSS** — `cdn.tailwindcss.com`
- **Alpine.js 3.x** — `cdn.jsdelivr.net/npm/alpinejs@3.x.x`

Three-panel layout: left (route tree + history), center (request builder), right (response panel). CSRF token sent via `X-CSRF-TOKEN` header.

## PHP requirements & style

- PHP 8.2+ (typed properties, promoted constructor properties)
- No code generation, no migrations, no queue jobs
- No test infrastructure yet (phpunit in require-dev but no phpunit.xml or test files)

## Key quirks

- **History is session-backed** — ephemeral, lost on session clear or server restart. No database persistence.
- **Route scanning is live** — reflects the host app's routes at scan time; excluded prefixes are configurable.
- **Body field only visible** for POST/PUT/PATCH (Alpine `x-show`).
- **Response headers are normalized** — single-value arrays flattened to string by `ApiClient::normalizeHeaders()`.
- **Pre-scripts field** — faker tags are parsed but the output is discarded; only side effects of faker generation (if any) matter.
- **No SPA routing** — the page is a single Blade view, all interactivity is Alpine.js on one `x-data` scope.
- **CSRF required** for POST/DELETE endpoints (web middleware group).
- **Route grouping** — routes are grouped by controller (default), URI prefix, or none. Sorting by URI, method, name, or controller.
- **Hash deep linking** — `#METHODuri` format (e.g. `#POSTapi/v1/users`) enables bookmarkable endpoint links.
