# Changelog

All notable changes to Laranova are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### Added

- Header toggle (enable/disable per header, matching query param behavior)
- Route Info tab moved to first position in the request builder
- Action (method) name displayed next to each route in the sidebar
- Distinguishable purple color for controller group prefixes in route tree
- Complete faker tag reference table in README with all 27 tags and examples
- CHANGELOG.md

### Changed

- `laranova/laranova` → `ug-code/laranova` (composer package name)
- Packagist badge and install commands updated to match new package name
- Composer.json: added `homepage` and author `homepage`
- README.md: fixed license link to point to correct GitHub repo
- Action label color: `text-gray-600` → `text-gray-500` for better readability

---

## [1.0.0] - YYYY-MM-DD

### Added

- Postman-like SPA interface at `/laranova` with Tailwind CSS + Alpine.js
- Route scanner: lists all application routes grouped by controller
- Request builder: method, URL, headers, query params, body, file upload
- Faker tag engine: 27 tag types (`{{ @faker:name }}`, `{{ @faker:email }}`, etc.)
- Pre-scripts: Postman-compatible `pm.*` JavaScript API
- Session-backed request history (last 100 items)
- Route Info panel showing controller, middleware, validation rules, path params
- cURL export with resolved faker values
- Postman Collection v2.1 export
- Variables panel with environment variable replacement (`{{baseUrl}}`)
- Configurable security scheme (bearer, basic, apikey)
- Header defaults, auto Content-Type, configurable faker locale & HTTP timeout
- Local-only guard (only loads in `local` environment)
- Auto-discovery via `composer.json` `extra.laravel.providers`
