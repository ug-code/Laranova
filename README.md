# Laranova

> **Postman-like API test interface, directly inside your Laravel application.**  
> Zero configuration, local-only, session-backed, and fully extensible.

[![Latest Version](https://img.shields.io/github/v/tag/ug-code/Laranova?label=version&sort=semver)](https://packagist.org/packages/ug-code/laranova)
[![PHP](https://img.shields.io/badge/PHP-8.2+-7b7fb5)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x_|_12.x-fb503b)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

---

## Why Laranova?

You're debugging an API endpoint, testing validation rules, or inspecting a third-party integration. Normally you'd switch to Postman, Insomnia, or cURL — copy URLs, manage tokens, keep windows in sync.

Laranova lives **inside your Laravel app**. It scans all your routes, auto-detects validation rules, generates fake data via Faker, and lets you send requests — all from a single-page interface served at `/laranova`.

---
<img width="1917" height="872" alt="image" src="https://github.com/user-attachments/assets/92594af0-b3e0-4270-af13-169de867abb1" />

## Features

| Feature | Description |
|---|---|
| **Route Scanner** | Lists every route in your app, grouped by controller. Shows middleware, validation rules, path parameters, and file upload fields at a glance. |
| **Request Builder** | Method, URL, headers, query params, and body — with environment variable replacement (`{{baseUrl}}`). |
| **Header Toggle** | Enable or disable individual headers on the fly without removing them. |
| **Query Param Toggle** | Same per-param enable/disable control, with a reset-to-defaults button. |
| **Faker Tag Engine** | Use `{{ @faker:name }}`, `{{ @faker:email }}`, `{{ @faker:int }}`, and 20+ other tags inside URLs, headers, query params, and body. Values are generated fresh on each request. |
| **Pre-scripts (Postman Compat)** | Run JavaScript before each request using the `pm.*` API — `pm.variables.set()`, `pm.environment.get()`, `pm.sendRequest()`. |
| **Session-Backed History** | Last 100 requests stored in session. Click to restore method + URL. |
| **Route Info Panel** | Select a route from the sidebar to see its controller, middleware stack, validation rules, and path parameters at a glance. |
| **cURL Export** | One-click copy as cURL command with resolved faker values. |
| **Postman Export** | Export the current request as a Postman Collection v2.1. |
| **Variables Panel** | Define reusable environment variables (`baseUrl`, `bearerToken`, etc.) with config defaults and localStorage persistence. |
| **Auto Security Headers** | Automatically pre-populates `Authorization: Bearer {{bearerToken}}` based on security config. |
| **File Upload Support** | Auto-detects file/image validation rules and renders file inputs. |

---

## Installation

```bash
composer require --dev ug-code/laranova
```

Laranova uses package auto-discovery. No manual service provider registration needed.

---

## Quick Start

Navigate to `/laranova` in your browser. That's it.

The interface only loads in `local` environment. If you need to test in other environments:

```bash
# config/app.php
'providers' => [
    // ...
    \Laranova\LaranovaServiceProvider::class,
],
```

Then set `APP_ENV=local` or temporarily remove the environment check from the provider.

---

## Usage

### Sending a Request

1. Select an HTTP method from the dropdown.
2. Enter the full URL (or use `{{baseUrl}}/api/endpoint`).
3. Add headers, query params, and body as needed.
4. Optionally write pre-scripts.
5. Click **Send Request**.

### Selecting Routes from the Sidebar

The left panel lists all application routes (excluding debug/telescope paths). Routes are grouped by controller. Click a route to auto-fill method, URL, query parameters, and body — including validation-rule-aware faker values.

### Using Faker Tags

Any text field supports faker tag replacement. Tags are resolved fresh on each request:

| Tag | Faker Method | Example Output |
|---|---|---|
| `{{ @faker:name }}` | `name()` | `Dr. Zachary Brown` |
| `{{ @faker:firstName }}` | `firstName()` | `John` |
| `{{ @faker:lastName }}` | `lastName()` | `Smith` |
| `{{ @faker:fullName }}` | `name()` | `Prof. Jane Doe` |
| `{{ @faker:email }}` | `email()` | `jane.doe@example.com` |
| `{{ @faker:safeEmail }}` | `safeEmail()` | `john.doe@example.org` |
| `{{ @faker:phone }}` | `phoneNumber()` | `+1-555-123-4567` |
| `{{ @faker:phoneNumber }}` | `phoneNumber()` | `555.123.4567` |
| `{{ @faker:address }}` | `address()` | `123 Main St, New York, NY 10001` |
| `{{ @faker:streetAddress }}` | `streetAddress()` | `456 Elm Street` |
| `{{ @faker:city }}` | `city()` | `New York` |
| `{{ @faker:country }}` | `country()` | `United States` |
| `{{ @faker:postcode }}` | `postcode()` | `10001` |
| `{{ @faker:text }}` | `text()` | `Lorem ipsum dolor sit amet, consectetur adipiscing elit...` |
| `{{ @faker:sentence }}` | `sentence()` | `The quick brown fox jumps over the lazy dog.` |
| `{{ @faker:paragraph }}` | `paragraph()` | `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.` |
| `{{ @faker:number }}` | `randomNumber()` | `83742` |
| `{{ @faker:randomDigit }}` | `randomDigit()` | `7` |
| `{{ @faker:int }}` | `randomNumber()` | `58392` |
| `{{ @faker:uuid }}` | `uuid()` | `550e8400-e29b-41d4-a716-446655440000` |
| `{{ @faker:url }}` | `url()` | `https://www.example.com` |
| `{{ @faker:date }}` | `date()` | `2024-03-15` |
| `{{ @faker:dateTime }}` | `dateTime()` | `2024-03-15 14:30:00` |
| `{{ @faker:dateFrom }}` | `dateTimeBetween(-10y, now)` | `2019-07-22` |
| `{{ @faker:dateTo }}` | `dateTimeBetween(now, +10y)` | `2028-11-03` |
| `{{ @faker:company }}` | `company()` | `Acme Corporation` |
| `{{ @faker:boolean }}` | `boolean()` | `true` |
| `{{ @faker:word }}` | `word()` | `synergize` |
| `{{ @faker:title }}` | `title()` | `Prof.` |

Unknown tag types fall back to `word`.  

---

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=laranova-config
```

```php
// config/laranova.php

return [

    'default_headers' => [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    ],

    'auto_content_type' => true,  // Auto-add Content-Type for body requests

    'faker' => [
        'locale' => env('LARANOVA_FAKER_LOCALE', 'en_US'),
    ],

    'http' => [
        'timeout'         => env('LARANOVA_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('LARANOVA_HTTP_CONNECT_TIMEOUT', 10),
    ],

    'history' => [
        'max_items' => env('LARANOVA_HISTORY_MAX', 100),
    ],

    'routes' => [
        'exclude_prefixes' => [
            'laranova', '_debugbar', '_ignition', 'telescope', 'sanctum',
        ],
    ],

    'security' => [
        'type'     => env('LARANOVA_SECURITY_TYPE', 'bearer'),  // null, bearer, basic, apikey
        'name'     => env('LARANOVA_SECURITY_NAME', 'api_key'),
        'position' => env('LARANOVA_SECURITY_POSITION', 'header'),
    ],

    'default_variables' => [
        'baseUrl'     => env('LARANOVA_DEFAULT_BASE_URL', 'http://pinsever.test'),
        'bearerToken' => '',
    ],
];
```

---

## Publishing Views

```bash
php artisan vendor:publish --tag=laranova-views
```

This publishes the Blade SPA to `resources/views/vendor/laranova/`. You can customize the UI directly. The frontend uses **Tailwind CSS** and **Alpine.js 3.x** from CDN — no npm build step required.

---

## Environment Variables

| Variable | Default | Description |
|---|---|---|
| `LARANOVA_FAKER_LOCALE` | `en_US` | Faker locale for generated data |
| `LARANOVA_HTTP_TIMEOUT` | `30` | HTTP client timeout (seconds) |
| `LARANOVA_HTTP_CONNECT_TIMEOUT` | `10` | HTTP connection timeout (seconds) |
| `LARANOVA_HISTORY_MAX` | `100` | Max history items stored in session |
| `LARANOVA_SECURITY_TYPE` | `bearer` | Default auth scheme (`bearer`, `basic`, `apikey`, or empty) |
| `LARANOVA_DEFAULT_BASE_URL` | `http://pinsever.test` | Default `{{baseUrl}}` variable value |

---

## Architecture

```
src/
├── LaranovaServiceProvider.php     # Routes, views, config, container bindings
├── Facades/Laranova.php            # Facade → ApiClient
├── Http/Controllers/
│   └── LaranovaController.php      # Readonly controller, 6 actions
├── Services/
│   ├── ScriptParser.php            # Regex faker-tag engine
│   ├── ApiClient.php               # Laravel HTTP::send() wrapper
│   └── RouteScanner.php            # Scans all routes, extracts rules & metadata
resources/views/
├── laranova.blade.php              # Single-file SPA (Alpine.js + Tailwind)
├── components/
│   ├── left-sidebar.blade.php      # Route tree + history panel
│   ├── center-panel.blade.php      # Request builder
│   ├── right-panel.blade.php       # Response viewer
│   └── settings-modal.blade.php    # Settings overlay
└── partials/
    ├── tab-headers.blade.php       # Header builder with toggle
    ├── tab-query-params.blade.php  # Query param builder with toggle
    ├── tab-body.blade.php          # Body editor
    ├── tab-pre-scripts.blade.php   # JavaScript pre-script editor
    └── tab-route-info.blade.php    # Selected route details
```

---

## Routes

All routes are prefixed with `/laranova` and use the `web` middleware:

| Method | URI | Action |
|---|---|---|
| GET | `/laranova/` | Returns the SPA view |
| POST | `/laranova/resolve` | Resolves faker tags & returns parsed request |
| GET | `/laranova/history` | Returns session history as JSON |
| POST | `/laranova/history` | Stores a history entry |
| DELETE | `/laranova/history` | Clears all history |
| GET | `/laranova/routes` | Returns all scanned routes as JSON |

Route names are prefixed with `laranova.*` (e.g., `laranova.index`, `laranova.resolve`).

---

## Pre-scripts API

Laranova implements a subset of the Postman `pm.*` API:

```javascript
// Variables
pm.variables.get('key');
pm.variables.set('key', 'value');
pm.variables.unset('key');
pm.variables.clear();
pm.variables.replace('Hello {{name}}');

// Environment (localStorage-backed)
pm.environment.get('key');
pm.environment.set('key', 'value');
pm.environment.unset('key');
pm.environment.clear();

// HTTP requests within pre-scripts
const response = await pm.sendRequest({ method: 'GET', url: '...' });
// callback style:
pm.sendRequest({ ... }, (err, res) => { ... });
```

---

## Development

```bash
composer install
```

No build step. The frontend uses Tailwind CSS and Alpine.js from CDN.

---

## License

MIT © [ug-code](https://github.com/ug-code/Laranova)
