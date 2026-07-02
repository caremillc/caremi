# Careminate Configuration Repository

The Configuration Repository provides centralized access to framework and application configuration.

It loads PHP config files from the `config` directory and allows values to be accessed using dot notation.

## Responsibilities

The Configuration Repository is responsible for:

- Loading configuration files
- Storing configuration values
- Reading values using dot notation
- Setting values at runtime
- Checking whether values exist
- Forgetting values
- Merging package configuration
- Exposing the `config()` helper

## Config Files

Configuration files are stored in:

```txt
config/

Example:

config/app.php
config/database.php
config/cache.php
config/session.php

Each file must return an array.

return [
    'name' => env('APP_NAME', 'Careminate'),
];
Registering the Config Provider

In bootstrap/app.php:

use Careminate\Foundation\Providers\ConfigServiceProvider;

$app->register(ConfigServiceProvider::class);
Reading Config Values
$name = config('app.name');

With a default value:

$timezone = config('app.timezone', 'UTC');
Getting the Repository
$config = config();

Then:

$config->get('app.name');
Setting Values
config(['app.debug' => false]);

Or:

config()->set('app.debug', false);
Checking Values
config()->has('app.name');
Forgetting Values
config()->forget('services.stripe.secret');
Getting All Config
$all = config()->all();
Merging Config

Package providers may merge their default configuration.

config()->merge('billing', [
    'currency' => 'USD',
    'trial_days' => 14,
]);
Example config/app.php
return [
    'name' => env('APP_NAME', 'Careminate'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),

    'providers' => [
        App\Providers\AppServiceProvider::class,
    ],
];
Bootstrap Example
use Careminate\Foundation\Application;
use Careminate\Foundation\ProviderRepository;
use Careminate\Foundation\Providers\ConfigServiceProvider;
use Careminate\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__));

$app = new Application(dirname(__DIR__));

$app->register(ConfigServiceProvider::class);

$app->setEnvironment((string) config('app.env', 'production'));

(new ProviderRepository($app))->load(config('app.providers', []));

$app->boot();

return $app;
Developer Use Cases
Configure application name
config('app.name');
Configure database driver
config('database.default');
Configure cache driver
config('cache.default');
Configure service providers
config('app.providers');
Merge package defaults
config()->merge('package', [
    'enabled' => true,
]);
End-User Impact

Configuration affects the final behavior of an application.

End users benefit from:

Safer production behavior
Correct URLs
Correct assets
Correct database connections
Correct mail settings
Better localization
Stable deployments
Best Practices
Use env() only inside config files.
Use config() in application code.
Keep sensitive values in .env.
Keep config files committed to version control.
Keep .env out of version control.
Use package config merging for reusable modules.
Do not mutate config unnecessarily at runtime.
Troubleshooting
Config value returns null

Check that the config file exists and returns an array.

config/app.php
config() helper not found

Run:

composer dump-autoload

Make sure the helper file is listed in Composer autoload files.

Config provider not registered

Make sure this exists in bootstrap/app.php:

$app->register(ConfigServiceProvider::class);
Environment value not applied

Make sure Env::load() runs before ConfigServiceProvider is registered.

Next Feature
Phase 1 — Feature 6: Application Lifecycle Events

---

# Feature 5 Complete

Next:

```txt
Phase 1 — Feature 6: Application Lifecycle Events