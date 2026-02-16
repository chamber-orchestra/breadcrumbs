# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A PHP library (`chamber-orchestra/breadcrumbs`) providing a breadcrumb collection class that implements `ArrayAccess`, `Iterator`, and `Countable`. It integrates with Symfony's `HttpFoundation` Request for route-based crumbs. Part of the `chamber-orchestra` bundle ecosystem.

## Commands

```sh
composer install                       # Install dependencies
composer test                          # Run full PHPUnit suite
vendor/bin/phpunit                     # Run full test suite directly
vendor/bin/phpunit --filter SomeTest   # Run specific test class/method
composer analyse                       # Run PHPStan
composer cs-check                      # Run PHP-CS-Fixer (dry-run)
```

## Architecture

Single-class library: `src/Breadcrumbs.php` in the `ChamberOrchestra\Breadcrumbs` namespace. PSR-4 autoloading maps `ChamberOrchestra\Breadcrumbs\` to `src/`.

## Code Style

- PHP ^8.5 with `declare(strict_types=1)` in every file
- PHP-CS-Fixer config in `.php-cs-fixer.dist.php` enforces: `@PER-CS` + `@Symfony` rulesets, strict native function invocation (backslash-prefix all native calls), no global namespace imports, alpha-ordered imports, single quotes, trailing commas in multiline
- One class/interface/trait per file matching the filename
- CI runs on PHP 8.5 via GitHub Actions

## Testing

- Tests go in `tests/` (mirroring `src/` structure), classes use `Test` suffix
- No tests exist yet

## CI/CD

- **php.yml:** Runs tests, PHPStan, and PHP-CS-Fixer on push/PR to main (PHP 8.5)
- **tag.yml:** Auto-tags a patch version bump on merged PRs to main
- **Dependabot:** Weekly updates for Composer and GitHub Actions dependencies
