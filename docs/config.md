# Careminate Support - Config Class Guide

## Overview
The `Config` class in the **Careminate PHP Framework** provides a centralized and cached configuration management system. It is responsible for loading configuration files from the `/config` directory, retrieving values, updating them in memory, and checking configuration existence.

This system is inspired by frameworks such as Laravel, offering a familiar syntax and performance-friendly design.

---

## Location
`framework-pro-mvc/Careminate/Support/Config.php`

---

## Core Responsibilities
- Load configuration files only when needed (lazy loading).
- Cache loaded configurations in memory (`static::$cache`) for performance.
- Provide helper methods to get, check, and set configuration values.
- Support **dot notation** (e.g., `app.debug`, `database.connections.mysql`).
- Integrate with the `Arr` helper for nested key access.

---

## Class Structure

### Namespace
```php
namespace Careminate\Support;
```

### Dependencies
- `Careminate\Support\Arr`
- PHP built-in: `file_exists`, `require`

### Protected Properties
```php
protected static array $cache = [];
```
Stores the configuration data loaded from files. Each file is loaded once and then reused from memory.

---

## Methods

### 1. `get(string $key, mixed $default = null): mixed`
Retrieves a configuration value using dot notation.

#### Parameters
- **$key** — The configuration key (e.g., `app.name`, `database.connections.mysql`).
- **$default** — Optional fallback value if the key does not exist.

#### Returns
- The configuration value or `$default` if not found.

#### Example
```php
$appName = Config::get('app.name');
$debugMode = Config::get('app.debug', false);
```

---

### 2. `has(string $key): bool`
Checks whether a given configuration key exists.

#### Parameters
- **$key** — The configuration key to check.

#### Returns
- **bool** — `true` if the key exists, otherwise `false`.

#### Example
```php
if (Config::has('database.connections.mysql')) {
    echo "MySQL configuration found.";
}
```

---

### 3. `set(string $key, mixed $value): void`
Updates a configuration value in the in-memory cache.  
Note: This does **not** write to the file system — only modifies the runtime copy.

#### Parameters
- **$key** — The configuration key.
- **$value** — The new value to set.

#### Example
```php
Config::set('app.debug', true);
```

---

### 4. `getFileFromKey(string $key): string`
Extracts the file name from the given configuration key.

#### Example
```php
Config::getFileFromKey('database.connections.mysql'); // returns 'database'
```

---

### 5. `getNestedKey(string $key): ?string`
Returns the nested portion of a key (after the file prefix).

#### Example
```php
Config::getNestedKey('database.connections.mysql'); // returns 'connections.mysql'
```

---

## How Configuration Files Work

Each configuration file is a PHP file located in the `/config` directory that returns an array of settings.

#### Example: `config/app.php`
```php
return [
    'name' => 'Careminate',
    'debug' => true,
    'timezone' => 'UTC',
];
```

You can then access values using:
```php
Config::get('app.name'); // "Careminate"
Config::get('app.debug'); // true
```

---

## Performance
The `Config` class caches all loaded configurations in memory via the static `$cache` array. Once a file is loaded, subsequent calls to the same configuration file will not hit the filesystem again during the same request lifecycle.

---

## Integration with the Arr Helper
The `Arr` class provides methods like `get`, `set`, and `has` for working with deeply nested arrays.  
`Config` relies on it for dot-notation access within configuration arrays.

---

## Example Usage

```php
use Careminate\Support\Config;

// Get a configuration value
$dbHost = Config::get('database.connections.mysql.host');

// Check if a configuration exists
if (Config::has('app.timezone')) {
    echo Config::get('app.timezone');
}

// Update configuration at runtime
Config::set('app.debug', false);
```

---

## Notes & Best Practices

- Always use `Config::get()` instead of directly including files.
- Avoid modifying config files at runtime — use `Config::set()` only for temporary overrides.
- Clear the cache if configuration values are dynamically changed between requests.
- Combine this with `.env` variables for secure and flexible configuration management.

---

## Summary

| Method | Purpose | Returns |
|---------|----------|----------|
| `get()` | Retrieve configuration value | mixed |
| `has()` | Check if a config key exists | bool |
| `set()` | Modify configuration in memory | void |
| `getFileFromKey()` | Extract config filename | string |
| `getNestedKey()` | Extract nested path | string |

---

**Careminate PHP Framework - Config System**  
Version: 1.0.0  
Author: CareMi Development Team  
License: MIT
