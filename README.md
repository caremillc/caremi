# Caremi

Caremi is the application project powered by the independently structured
`caremillc/framework` package.

## Requirements

- PHP 8.4 or newer
- Composer 2
- dom
- json
- libxml
- mbstring
- tokenizer
- xml
- xmlwriter

## Installation

```powershell
$env:Path = "C:\xampp\php;$env:Path"
Set-Location C:\xampp\htdocs\caremi

php --version
composer --version
composer validate --strict
composer update
composer test
composer audit

Commit the generated root composer.lock.

Framework quality checks
Set-Location C:\xampp\htdocs\caremi\framework

composer validate --strict
composer update
composer quality
composer audit
Current capabilities

Phase 1 provides:

repository and package boundaries;
coding standards;
tests and static analysis;
CI;
foundational exceptions;
support utilities;
versioning and ADRs.

Phase 2 adds:

public container contracts;
PSR-11 dependency resolution;
binding lifetimes;
contextual and primitive injection;
aliases and tagged services;
constructor autowiring;
injection and lazy attributes;
factories;
independent scopes;
circular-dependency diagnostics;
container compilation and cache loading.

The application kernel is introduced in Phase 3.


# Commands

Because the package metadata changed, remove stale dependency locks and reinstall. If the existing root lock still records `0.1.0-dev`, use Composer’s update command rather than manually editing it.

```powershell
$env:Path = "C:\xampp\php;$env:Path"

Set-Location C:\xampp\htdocs\caremi\framework

composer validate --strict
composer update
composer dump-autoload --optimize
composer test
composer analyse
composer cs:check
composer refactor:check
composer audit

Then:

Set-Location C:\xampp\htdocs\caremi

composer validate --strict
composer update careminate/framework phpunit/phpunit --with-all-dependencies
composer dump-autoload --optimize
composer test
composer audit

Run just the container suite:

Set-Location C:\xampp\htdocs\caremi\framework

php vendor\bin\phpunit --configuration phpunit.xml.dist tests\Unit\Container

Security review
Compiled cache files are executable PHP and must remain outside public.
The web server must not be allowed to replace production cache files.
Container IDs must never be selected directly from untrusted request input.
Scoped services isolate request/job state and must be closed reliably.
Cache generation rejects closures, objects, and resources.
Factory failures retain their original exceptions for diagnosis.
No PHP deserialization is used.
Duplicate registrations fail rather than silently changing behavior.
Primitive values may contain secrets; sensitive values should not be compiled until Phase 5 introduces secret-aware configuration.
Performance review
Singleton and scoped caches use direct keyed lookups.
Reflection classes are cached for the lifetime of the container.
Lazy services use native PHP 8.4 proxies.
Compiled caches avoid replaying registration logic.
Independent scope objects support long-running workers without a mutable global “current request.”
Ahead-of-time constructor code generation is intentionally deferred until benchmark work demonstrates its value.
Acceptance criteria

Before Phase 2 can be verified complete:

 PHP 8.4 or PHP 8.5 is active in XAMPP CLI.
 Both Composer packages validate.
 psr/container:^2.0 installs successfully.
 All framework tests pass.
 Root integration tests pass.
 PHPStan level max reports zero errors.
 PHP-CS-Fixer reports no changes.
 Rector dry-run reports no changes.
 Bindings, aliases, tags, factories, and autowiring work.
 Singleton and scoped lifetime tests pass.
 Contextual and primitive bindings pass.
 Native lazy initialization test passes.
 Circular-dependency paths are actionable.
 Compiled cache loads into a frozen container.
 Composer audits report no vulnerable dependencies.