# Careminate Application Core

The Application Core is the root object of the Careminate Framework. It represents the running application and provides access to important framework paths, environment state, version information, and bootstrap status.

## Responsibilities

The Application Core is responsible for:

- Managing the application base path
- Providing framework version information
- Managing the current environment
- Resolving framework paths
- Creating runtime storage directories
- Tracking bootstrap state

## Creating the Application

The application is usually created inside `bootstrap/app.php`.

```php
use Careminate\Foundation\Application;

$app = new Application(dirname(__DIR__));

return $app;
```

## Application Version
```php
$app->version();
```

Returns the current Careminate Framework version.

## Environment

Set the environment:
```php
$app->setEnvironment('local');
```

## Get the environment:
```php
$app->environment();
```

## Example:
```php
if ($app->environment() === 'production') {
    // Run production-only logic
}

```

## Paths

The Application Core provides centralized path helpers.

## Base Path
```php
$app->basePath();
$app->basePath('composer.json');
```

## App Path
```php
$app->appPath();
$app->appPath('Http/Controllers');
```

## Config Path
```php
$app->configPath();
$app->configPath('app.php');
```

## Public Path
```php
$app->publicPath();
$app->publicPath('assets/css/app.css');
```

## Storage Path
```php
$app->storagePath();
$app->storagePath('logs/app.log');
```
## Resource Path
```php
$app->resourcePath();
$app->resourcePath('views/home.caremi.php');
```

## ootstrap Path
```php
$app->bootstrapPath();
$app->bootstrapPath('cache');
```

## Runtime Directories

When the application is created, Careminate automatically ensures that the following directories exist:

```text
storage/
storage/framework/
storage/framework/cache/
storage/framework/views/
storage/framework/sessions/
storage/logs/
```

This prevents common runtime errors caused by missing storage directories.

## Bootstrap State

Check whether the application has finished bootstrapping:
```php
$app->isBootstrapped();
```

Mark the application as bootstrapped:
```php
$app->markAsBootstrapped();
```

## Developer Use Case

A package or service provider may need to write cached files:

$cachePath = $app->storagePath('framework/cache/routes.php');
End-User Impact

The Application Core improves stability by ensuring the framework knows where files, configuration, resources, logs, and runtime data should be stored.

End users benefit from:

More reliable application startup
Better deployment behavior
Fewer runtime path errors
Cleaner framework organization
Best Practices
Do not hard-code absolute paths.
Always use the Application path methods.
Set the environment during bootstrap.
Keep runtime files inside the storage directory.
Do not expose storage files directly through the public directory unless explicitly intended.

## Next Feature

After the Application Core, the next feature is the Service Container.


---

# Feature 1 Complete

The **Application Core** is now defined.

Next feature:

```txt
Phase 1 — Feature 2: Service Container / IoC / Dependency Injection
```
---


