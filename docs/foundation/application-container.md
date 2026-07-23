# Feature 001 — Foundation, Container, and Application Lifecycle

## Purpose

This feature supplies the runtime foundation used by every later Careminate feature. It creates objects, injects dependencies, manages service lifetimes, registers service providers, exposes normalized paths, and coordinates application boot and termination.

## Container bindings

### Transient binding

A transient binding creates a new object every time it is resolved.

```php
use App\Contracts\Clock;
use App\Services\SystemClock;

$app->bind(Clock::class, SystemClock::class);
$first = $app->make(Clock::class);
$second = $app->make(Clock::class);

assert($first !== $second);
```

### Singleton binding

A singleton is constructed once and reused for the application lifetime.

```php
$app->singleton(Clock::class, SystemClock::class);

assert($app->make(Clock::class) === $app->make(Clock::class));
```

### Existing instance

```php
use DateTimeZone;

$app->instance(DateTimeZone::class, new DateTimeZone('UTC'));
```

### Scoped binding

A scoped service is shared only within one request, job, command, or message scope.

```php
$app->scoped(RequestContext::class);
$app->beginScope('request-123');

$first = $app->make(RequestContext::class);
$second = $app->make(RequestContext::class);
assert($first === $second);

$app->endScope();
```

Resolving a scoped service without an active scope throws `BindingResolutionException`. The front controller starts and closes its HTTP scope automatically.

## Constructor autowiring

Concrete classes do not need explicit bindings when all constructor dependencies can be resolved.

```php
final readonly class AuditService
{
    public function __construct(
        private Clock $clock,
        private UserRepository $users,
    ) {
    }
}

$audit = $app->make(AuditService::class);
```

Interfaces must be bound to an implementation. Scalar constructor parameters must use a factory, a default value, or named runtime parameters.

```php
$mailer = $app->make(SmtpMailer::class, [
    'host' => 'smtp.example.com',
    'port' => 587,
]);
```

Runtime parameters are deliberately rejected for singleton and scoped bindings because changing arguments for a shared object would be ambiguous.

## Factory bindings

Factories receive the container and named runtime parameters.

```php
use Careminate\Contracts\Container\ContainerInterface;

$app->singleton(ApiClient::class, static function (
    ContainerInterface $container,
    array $parameters,
): ApiClient {
    return new ApiClient(
        baseUrl: 'https://api.example.com',
        clock: $container->make(Clock::class),
    );
});
```

## Contextual bindings

Contextual bindings let two consumers receive different implementations of the same contract.

```php
$app->bind(Logger::class, FileLogger::class);

$app->when(PaymentService::class)
    ->needs(Logger::class)
    ->give(AuditLogger::class);
```

`PaymentService` receives `AuditLogger`; other consumers receive `FileLogger`.

## Method invocation

`call()` injects object dependencies and accepts named scalar parameters.

```php
$result = $app->call(
    static fn (Clock $clock, string $name): string =>
        $name . ' at ' . $clock->now()->format(DATE_ATOM),
    ['name' => 'Careminate'],
);
```

Supported callable forms include closures, functions, invokable objects, `[ClassName::class, 'method']`, `ClassName::class . '::staticMethod'`, and `ClassName::class . '@method'`.

## Service providers

Providers register related services as a feature module.

```php
namespace App\Providers;

use App\Contracts\Clock;
use App\Services\SystemClock;
use Careminate\Contracts\Application\ApplicationInterface;
use Careminate\Foundation\Providers\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(ApplicationInterface $app): void
    {
        $app->singleton(Clock::class, SystemClock::class);
    }

    public function boot(ApplicationInterface $app): void
    {
        // Run work that requires every provider to be registered.
    }
}
```

Register providers in `bootstrap/app.php`. `register()` is executed immediately; `boot()` runs when the application boots. Registering the same provider class more than once returns the original provider and does not register it twice.

## Application lifecycle

The valid lifecycle is:

```text
created -> booting -> booted -> terminating -> terminated
                    
                    -> failed -> terminating -> terminated
```

Callbacks are registered before boot:

```php
$app->booting(static function (ApplicationInterface $app): void {
    // Before provider boot methods.
});

$app->booted(static function (ApplicationInterface $app): void {
    // After every provider has booted.
});

$app->terminating(static function (ApplicationInterface $app): void {
    // Release resources and flush buffered work.
});
```

Termination callbacks execute in reverse registration order. Every callback is attempted even if one fails; the first failure is rethrown after cleanup.

## Paths

```php
$app->basePath();
$app->appPath('Services');
$app->configPath('app.php');
$app->databasePath('migrations');
$app->publicPath('assets');
$app->resourcePath('views');
$app->routePath('web.php');
$app->storagePath('logs');
```

Override a path before booting:

```php
$app->usePath('storage', 'var/storage');
```

Relative overrides are resolved from the application base path.

## End-user behavior

When the application is healthy, users receive an HTTP 200 JSON response containing the framework name, status, and UTC timestamp. Internal filesystem paths and exception details are never returned. Unexpected errors produce a generic HTTP 500 JSON response while detailed diagnostics go to the server error log.

## Testing

```bash
composer test
```

The suite verifies autowiring, lifetimes, named parameters, aliases, circular dependencies, contextual bindings, request scopes, factory side-effect prevention, callable formats, core binding restoration, provider deduplication, lifecycle ordering, failed boot cleanup, path normalization, and application reset restrictions.

## Production checklist

- Use PHP 8.3 or newer in both Apache and the command line.
- Serve only the `public/` directory.
- Run `composer install --no-dev --classmap-authoritative` during deployment.
- Disable `display_errors` and enable server-side error logging.
- Set `APP_TIMEZONE` to a valid PHP timezone when UTC is not desired.
- Use one new scope per HTTP request, queue job, command, or consumed message.
- Always terminate the application in a `finally` block.



32. C:\xampp\htdocs\caremi\README.md

# Careminate Framework Foundation

Careminate is an incremental PHP framework project. This foundation release provides a PSR-11-compatible dependency injection container, application lifecycle management, service providers, path management, request scopes, a production-safe JSON front controller, and automated tests.

## Requirements

- PHP 8.3 or newer
- Composer 2
- Required PHPUnit extensions for development: DOM, JSON, mbstring, XML, and XMLWriter
- Apache, Nginx, or PHP's development server with `public/` as the document root

## Installation

```bash
composer install
composer test
php -S 127.0.0.1:8000 -t public
```

Visit `http://127.0.0.1:8000`.

Expected response:

```json
{
    "name": "Careminate",
    "status": "booted",
    "time": "2026-07-20T12:00:00+00:00"
}
```

## XAMPP setup

1. Copy the project to `C:\xampp\htdocs\careminate`.
2. Run `C:\xampp\php\php.exe composer.phar install`, or use your installed Composer command.
3. Point the Apache virtual-host document root to `C:\xampp\htdocs\careminate\public`.
4. Restart Apache after changing PHP classes when OPcache is enabled.
5. Run tests with `C:\xampp\php\php.exe tests\run.php`.

Do not point the web server at the project root because that can expose application, test, and vendor files.

## Documentation

See [`docs/features/001-foundation-container.md`](docs/features/001-foundation-container.md) for bindings, autowiring, providers, contextual dependencies, request scopes, lifecycle callbacks, and operational guidance.

