# Careminate Framework Environment & Bootstrap Setup Guide

This guide explains how to set up environment variables, bootstrap the framework, and initialize the main application entry point for **Careminate**.

---

## Step 1: `.env` — Main Environment Configuration

This file stores the application's environment-specific variables.  
Edit these values as needed for your local or production environment.

```dotenv
APP_NAME=Careminate
# true for dev, false for prod 
APP_ENV=develoment 
APP_DEBUG=true 
APP_URL=http://localhost
ASS_URL=http://localhost

# HTTP Configuration
REDIRECT_PRESERVE_RELATIVE=true 

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dev_caremi
DB_USERNAME=root
DB_PASSWORD=
```

---

## Step 2: `.env.example` — Example Environment Template

This file acts as a template for `.env`.  
It should be version-controlled and distributed to other developers.

```dotenv
APP_NAME=Careminate
APP_ENV=develoment # true for dev, false for prod 
APP_DEBUG=true 
APP_URL=http://localhost
ASS_URL=http://localhost

REDIRECT_PRESERVE_RELATIVE=true # http 

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dev_caremi
DB_USERNAME=root
DB_PASSWORD=
```

> **Tip:** When setting up a new environment, copy this file as `.env` and modify values accordingly.

---

## Step 3: `\bootstrap\app.php` — Framework Bootstrapper

This file initializes your Careminate application, loads environment variables, and configures the debug mode.

```php
<?php declare(strict_types=1);

// ---------------------------------------------------------
// Define application constants
// ---------------------------------------------------------
define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('BOOTSTRAP_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');

// ---------------------------------------------------------
// Load Composer's autoloader (if available)
// ---------------------------------------------------------
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    die('Autoloader not found. Please run "composer install".');
}

// ---------------------------------------------------------
// Load performance utilities
// ---------------------------------------------------------
require_once BOOTSTRAP_PATH . '/performance.php';

// ---------------------------------------------------------
// Simple .env loader
// ---------------------------------------------------------
$envFile = base_path('.env');
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// ---------------------------------------------------------
// Set error handling
// ---------------------------------------------------------
if (!function_exists('app_debug_mode')) {
    function app_debug_mode(): bool
    {
        return filter_var(env('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOL);
    }
}

if (app_debug_mode()) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// ---------------------------------------------------------
// Register global shutdown handler
// ---------------------------------------------------------
register_shutdown_function('logExecutionTime');

?>
```

---

## Step 4: `\config\app.php` — Core Application Config

Stores default app settings loaded via the `env()` helper.

```php
<?php declare(strict_types=1);

return [
    'name'  => env('APP_NAME') ?? 'Careminate',
    'env'   => env('APP_ENV') ?? 'production',
    'debug' => env('APP_DEBUG'),
    'url'   => env('APP_URL') ?? 'http://localhost',
    'timezone' => 'UTC',
    'key'   => 'frm_phpanonymous',
];

?>
```

> This file centralizes all key application-level configurations used throughout the system.

---

## Step 5: `\framework-pro-mvc\Careminate\Support\Helpers\functions.php` — Global Helpers

Defines useful global helper functions including `value()` and `env()`.

```php
<?php 
if (!function_exists('value')) {
    function value(mixed $value): mixed
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        if (array_key_exists($key, $_ENV)) {
            $value = $_ENV[$key];
        } elseif (array_key_exists($key, $_SERVER)) {
            $value = $_SERVER[$key];
        } else {
            $g = getenv($key);
            $value = ($g !== false) ? $g : $default;
        }

        if (!is_string($value)) {
            return $value;
        }

        $trimmedValue = trim($value);

        return match (strtolower($trimmedValue)) {
            'true' => true,
            'false' => false,
            'null' => null,
            'empty' => '',
            default => is_numeric($trimmedValue)
                ? (str_contains($trimmedValue, '.') ? (float)$trimmedValue : (int)$trimmedValue)
                : (preg_match('/^[\[{].*[\]}]$/', $trimmedValue) ? (json_decode($trimmedValue, true) ?? $trimmedValue) : $trimmedValue)
        };
    }
}
?>
```

---

## Step 6: `public/index.php` — Front Controller

This is the main entry point for all web requests.

```php
<?php declare(strict_types=1);

use Careminate\Http\Kernel;
use Careminate\Exceptions\Handler;
use Careminate\Exceptions\AuthException;
use Careminate\Http\Requests\Request;

require __DIR__ . '/../bootstrap/app.php';

try {
    $request = Request::createFromGlobals();
    $kernel = new Kernel();
    $response = $kernel->handle($request);
    $response->send();
} catch (AuthException $e) {
    $handler = new Handler();
    $handler->render($request ?? null, $e)->send();
} catch (\Throwable $e) {
    if (getenv('APP_DEBUG') === 'true') {
        echo "<pre>" . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . "</pre>";
        exit;
    }
    $handler = new Handler();
    $handler->render($request ?? null, $e)->send();
}

echo '<br><small>Environment: ' . config('app.env') . ' | Debug: ' . (config('app.debug') ? 'ON' : 'OFF') . '</small>';

?>
```

---

## ✅ Summary

| Step | File | Purpose |
|------|------|----------|
| 1 | `.env` | Environment-specific configuration |
| 2 | `.env.example` | Template for `.env` |
| 3 | `bootstrap/app.php` | Framework bootstrap and `.env` loader |
| 4 | `config/app.php` | Application configuration |
| 5 | `framework-pro-mvc/Careminate/Support/Helpers/functions.php` | Core helper functions |
| 6 | `public/index.php` | Application front controller |

---

## ⚙️ Notes

- Run `composer install` before starting the application.
- Ensure `.env` exists in your root directory.
- The `APP_DEBUG` flag controls error visibility.
- Use `APP_ENV` to switch between environments (`development` / `production`).

---
