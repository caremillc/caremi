# Contributing to Careminate

## Prerequisites

- PHP 8.4 or newer
- Composer 2
- Git

## Local setup

```bash
composer install
composer quality
composer rector-check
```

Run `composer format` to apply the coding standard. Run `composer mutation` before submitting changes to critical foundation code.

## Change requirements

1. Keep public code contract-first and compatible within a major release.
2. Add precise exceptions rather than throwing a generic root exception.
3. Add tests for success, invalid input, edge cases, and regressions.
4. Update the relevant feature guide and changelog.
5. Add an ADR when a decision materially changes package boundaries, lifecycle behavior, compatibility, security, or persistence.
6. Never commit credentials, local environment files, generated coverage, or dependency directories.

## Pull requests

Pull requests must pass tests, PHPStan at maximum level, coding-style checks, Rector dry-run, dependency audit, and the supported PHP/OS CI matrix. A breaking change must include a migration path and deprecation analysis.

