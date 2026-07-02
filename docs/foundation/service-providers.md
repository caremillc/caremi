# Careminate Service Providers

Service Providers are the central bootstrapping mechanism of the Careminate Framework.

They are responsible for registering services into the container and booting framework or application features.

## Responsibilities

Service Providers handle:

- Container bindings
- Singleton registration
- Interface implementation mapping
- Framework service registration
- Package registration
- Route loading
- Event listener registration
- Deferred service loading
- Application boot logic

## Creating a Provider

A provider extends `Careminate\Foundation\ServiceProvider`.

```php
use Careminate\Foundation\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
```

## Register Method

Use register() to bind services into the container.
```php
public function register(): void
{
    $this->app->singleton(UserService::class);
}
```

The register() method should not depend on services that may not yet be available.

Boot Method

Use boot() for logic that runs after all eager providers have registered.
```php
public function boot(): void
{
    $router = $this->app->make('router');

    $router->loadRoutesFrom($this->app->basePath('routes/web.php'));
}
```

The boot() method is useful for:

Loading routes
Registering event listeners
Publishing package resources
Registering view namespaces
Registering macros
Registering Providers

Providers are usually loaded in bootstrap/app.php.
```php
(new ProviderRepository($app))->load([
    AppServiceProvider::class,
]);
```
Then boot the application:
```php
$app->boot();
```

## Deferred Providers

Deferred providers are not loaded immediately.

They are loaded only when one of their provided services is requested.
```php

class ReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('reports', ReportManager::class);
    }

    public static function provides(): array
    {
        return [
            'reports',
            ReportManager::class,
        ];
    }
}
```

Detecting Deferred Providers

A provider is considered deferred when provides() returns services.
```php
public static function provides(): array
{
    return ['reports'];
}
```

## Provider Manifest

Careminate stores a provider manifest in:

storage/framework/cache/providers.php

The manifest contains:

- All configured providers
- Eager providers
- Deferred service mappings

This improves startup performance.

Booting Callbacks

Register callbacks before providers boot:
```php
$app->booting(function ($app) {
    //
});
```

Register callbacks after providers boot:
```php
$app->booted(function ($app) {
    //
});
```

Checking Loaded Providers
$providers = $app->getLoadedProviders();
Example: App Provider
```php 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('app.name', fn () => 'Careminate');
    }
}
```

Example: Package Provider
```php

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BillingManager::class);
    }

    public function boot(): void
    {
        $this->app->make('router')
            ->loadRoutesFrom(__DIR__ . '/../routes/billing.php');
    }
}
```

Developer Use Cases
Register a repository
```php
$this->app->bind(
    UserRepositoryInterface::class,
    DatabaseUserRepository::class
);
```

Register a singleton manager
```php
$this->app->singleton(CacheManager::class);
```

Register package commands
```php
public function boot(): void
{
    $this->app->make('console')->commands([
        BillingInstallCommand::class,
    ]);
}
```

Lazy-load an expensive service
```php
public static function provides(): array
{
    return [
        SearchManager::class,
    ];
}
```

## End-User Impact

Service Providers improve application performance and reliability.

End users benefit from:

Faster response times
Cleaner modular features
Better package integration
Services loaded only when needed
More reliable application startup
Best Practices
Put container bindings in register().
Put route/event/view boot logic in boot().
Do not perform heavy work in provider constructors.
Use deferred providers for expensive or rarely used services.
Keep providers focused on one feature or package.
Avoid resolving services inside register() unless absolutely necessary.
Use interfaces for replaceable services.
Troubleshooting
Provider class does not exist

Run:

composer dump-autoload

Then check the namespace and class name.

Deferred service does not resolve

Make sure the service key appears in provides().
```php
public static function provides(): array
{
    return ['reports'];
}
```

Service provider boots too early

Move logic from register() to boot().

Provider cache is stale

Delete:

storage/framework/cache/providers.php

Then reload the application.

Next Feature

The next foundation feature is:

Phase 1 — Feature 4: Environment Loader

---

# Feature 3 Complete

Next:

```txt
Phase 1 — Feature 4: Environment Loader

