# Careminate Development Workspace

Careminate is a modular, contract-first PHP framework targeting PHP 8.4 and later supported versions.

This repository contains two related but independently defined Composer packages:

1. The Careminate reference application and development workspace.
2. The Careminate framework package located in `framework/`.

## Requirements

- PHP 8.4 or later within the supported compatibility range
- Composer 2
- Ctype extension
- Filter extension
- JSON support
- Mbstring extension
- OpenSSL extension

Development and testing additionally require the extensions required by PHPUnit and the configured engineering tools.

## Repository structure

```text
caremi/
├── app/
├── bootstrap/
├── config/
├── docs/
├── modules/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── framework/
│   ├── src/
│   ├── tests/
│   ├── docs/
│   └── composer.json
└── composer.json
```
Install

From the project root:

composer install


Composer loads the local framework using the path repository declared in the root composer.json.

Tests

Run all tests:

composer test


Run only reference-application tests:

composer test:application


Run only framework tests:

composer test:framework

Static analysis
composer analyse

Coding standards
composer style

Rector verification
composer refactor:check

Dependency audit
composer audit

Complete quality verification
composer quality

Framework namespace

Framework classes use:

Careminate\


Application classes use:

App\


Local application modules use:

Modules\


The framework package must never depend on App\ or Modules\.

Current development milestone

Milestone 1: Bootable Foundation.

The current implementation stage establishes the repository and engineering foundation.

Application lifecycle, dependency injection, service providers, modules, HTTP handling, routing, persistence, and other higher-level systems are implemented only in their assigned stages.

Security

Do not commit credentials, API tokens, passwords, private keys, production environment files, or other secrets.

Local environment values belong in .env, which is excluded from version control.

The committed .env.example file must contain only non-sensitive examples.


---