# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A standalone PHP library (not a Symfony bundle) for building ordered breadcrumb trail data structures. Single class, no DI container, no services.

**Requirements:** PHP ^8.5, `symfony/http-foundation` 8.0.*

**Namespace:** `ChamberOrchestra\Breadcrumbs` (PSR-4 from package root — no `src/` directory)

## Commands

```bash
composer install                        # Install dependencies
./bin/phpunit                           # Run all tests
./bin/phpunit --filter ClassName        # Run a specific test class
./bin/phpunit --filter testMethodName   # Run a specific test method
composer test                           # Alias for vendor/bin/phpunit
```

## Architecture

The entire package is one file: `Breadcrumbs.php`. The class implements `ArrayAccess`, `Iterator`, and `Countable`. Each crumb is a plain associative array with keys `name`, `route`, `routeParams`.

Key methods:
- `addCrumb(string $name, ?string $route, array $params, bool $prepend)` — append or prepend a crumb
- `addRequestCrumb(string $name, Request $request)` — extract route info from a Symfony Request
- `addCrumbsClosure(Closure $closure)` — pass `$this` into a closure for fluent batch-adding
- `getCrumbs()` — return the raw crumbs array

## Testing

- PHPUnit 13.x; tests in `tests/` autoloaded as `Tests\`
- Unit tests in `tests/Unit/` extend `TestCase`

## Code Conventions

- PSR-12, `declare(strict_types=1)`, 4-space indent
- Typed properties and return types; favor `readonly`
- Commit style: short, action-oriented with optional bracketed scope — `[fix] ...`, `[master] ...`
