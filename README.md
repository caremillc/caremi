# Caremi

Caremi is the application project powered by the independently structured
`caremillc/framework` package.

## Requirements

- PHP 8.2 or newer
- Composer 2
- Required PHP extensions for Composer and PHPUnit:
  - dom
  - json
  - libxml
  - mbstring
  - tokenizer
  - xml
  - xmlwriter

## Repository layout

- `app/` — application-specific PHP code
- `config/` — application configuration
- `public/` — web server document root
- `storage/` — application-generated runtime data
- `tests/` — application and framework integration tests
- `framework/` — reusable Careminate framework package

## Installation

From `C:\xampp\htdocs\caremi`:

```powershell
$env:Path = "C:\xampp\php;$env:Path"

php --version
composer --version
composer validate --strict
composer update
composer test
```
---


The first composer update creates the root composer.lock. Commit the root
lock file because Caremi is an application.

The nested framework/composer.lock is intentionally ignored because
caremillc/framework is a reusable library.

## Framework quality checks
```powershell
Set-Location C:\xampp\htdocs\caremi\framework

composer validate --strict
composer update
composer quality
composer audit
```
---

## Current phase
Phase 1 provides only the engineering foundation:

- package boundaries;
- autoloading;
- exception foundations;
- support utilities;
- version management;
- tests;
- static analysis;
- code formatting;
- automated refactoring checks;
- continuous integration;
- architecture decision records.

Runtime application bootstrapping begins in the next framework phase.