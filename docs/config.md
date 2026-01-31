# Careminate Support - Config Class Guide

## Overview
The `Config` class in the **Careminate PHP Framework** provides a centralized and cached configuration management system. It is responsible for loading configuration files from the `/config` directory, retrieving values, updating them in memory, and checking configuration existence.

This system is inspired by frameworks such as Laravel, offering a familiar syntax and performance-friendly design.

---

## Location
`src/Supports/Config.php`

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
namespace Careminate\Supports;
```

### Dependencies
- `Careminate\Supports\Arr`
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
use Careminate\Supports\Config;

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



step 6: doc\utilities.md 
# Careminate Framework - General Utilities Guide


Careminate Framework - General Utilities Guide

This guide covers the main utility classes of the Careminate framework, including Arr, Collection, Config, Macroable, and Str, with explanations and use cases.

Table of Contents

* [Arr Utility Guide](./arr.md)
* [Collection Utility Guide](./collection.md)
* [Str Utility Guide](./str.md)
* [Macroable Utility Guide](./macroable.md)

## 1. Array Utilities (`Arr`)

**File:** `\Careminate\Supports\Arr.php`

**Description:**
The `Arr` class provides a set of static methods for working with PHP arrays. It supports nested arrays with dot notation, array transformations, filtering, and more.

**Key Methods & Use Cases:**

* `Arr::add($array, $key, $value)` — Add a value if it doesn't exist.
* `Arr::get($array, $key, $default)` — Retrieve a value using dot notation.
* `Arr::set($array, $key, $value)` — Set a value using dot notation.
* `Arr::has($array, $key)` — Check if a key exists.
* `Arr::forget($array, $key)` — Remove a key.
* `Arr::pluck($array, $value, $key)` — Extract values from a multi-dimensional array.
* `Arr::flatten($array, $depth)` — Flatten multi-dimensional arrays.
* `Arr::random($array, $number)` — Retrieve one or more random values.
* `Arr::wrap($value)` — Wrap a value in an array if it isn’t already.

**Example:**

```php
use Careminate\Supports\Arr;

$array = ['user' => ['name' => 'Alice', 'age' => 25]];
$name = Arr::get($array, 'user.name'); // "Alice"
Arr::set($array, 'user.email', 'alice@example.com');
```

---

## 2. Collection Utilities (`Collection`)

**File:** `\Careminate\Supports\Collection.php`

**Description:**
The `Collection` class wraps arrays to provide a fluent interface for transforming, filtering, and aggregating data.

**Key Methods & Use Cases:**

* `Collection::make($items)` — Create a new collection.
* `map($callback)` — Transform each item.
* `filter($callback)` — Filter items based on a callback.
* `first($callback, $default)` — Get the first item or a default value.
* `last($callback, $default)` — Get the last item.
* `sum($key)` / `avg($key)` / `min($key)` / `max($key)` — Aggregate numeric values.
* `pluck($key)` — Extract a list of values.
* `groupBy($key)` — Group items by a key or callback.
* `unique($key)` — Remove duplicate items.
* `shuffle()` / `random($amount)` — Randomize collection order.

**Example:**

```php
use Careminate\Supports\Collection;

$users = Collection::make([
    ['name' => 'Alice', 'age' => 25],
    ['name' => 'Bob', 'age' => 30],
]);

$names = $users->pluck('name'); // ["Alice", "Bob"]
$adults = $users->filter(fn($u) => $u['age'] >= 30);
```

---

## 3. String Utilities (`Str`)

**File:** `\Careminate\Supports\Str.php`

**Description:**
The `Str` class offers helpers for manipulating strings, such as casing, limiting, random strings, and slug generation.

**Key Methods & Use Cases:**

* `Str::camel($value)` — Convert string to camelCase.
* `Str::snake($value)` — Convert string to snake_case.
* `Str::kebab($value)` — Convert string to kebab-case.
* `Str::title($value)` — Convert string to Title Case.
* `Str::lower($value)` / `Str::upper($value)` — Change letter case.
* `Str::limit($value, $limit, $end)` — Limit string length.
* `Str::contains($haystack, $needles)` — Check if string contains a value.
* `Str::startsWith($haystack, $needles)` / `Str::endsWith($haystack, $needles)` — Prefix/suffix check.
* `Str::slug($title, $separator)` / `Str::slugify($text, $options)` — Generate URL-friendly slugs.
* `Str::random($length)` — Generate a random string.

**Example:**

```php
use Careminate\Supports\Str;

$slug = Str::slug('Hello World!'); // "hello-world"
$camel = Str::camel('hello_world'); // "helloWorld"
```

---

## 4. Macroable Trait (`Macroable`)

**File:** `\Careminate\Supports\Macroable.php`

**Description:**
The `Macroable` trait allows adding dynamic methods to classes at runtime. Methods can be added either statically or to instances.

**Key Methods & Use Cases:**

* `macro($name, $callable)` — Register a macro.
* `hasMacro($name)` — Check if a macro exists.
* `__call($method, $parameters)` — Dynamically call instance macros.
* `__callStatic($method, $parameters)` — Dynamically call static macros.

**Example:**

```php
use Careminate\Supports\Macroable;

class MyClass {
    use Macroable;
}

MyClass::macro('greet', function($name) { return "Hello, $name!"; });
echo (new MyClass)->greet('Alice'); // "Hello, Alice!"
```

---

## 5. Configuration Utilities (`Config`)

**File:** `\Careminate\Supports\Config.php`

**Description:**
The `Config` class provides a simple interface to read and write application configuration stored in PHP files.

**Key Methods & Use Cases:**

* `Config::get($key, $default)` — Retrieve a configuration value.
* `Config::has($key)` — Check if a configuration key exists.
* `Config::set($key, $value)` — Set a configuration value.

**Example:**

```php
use Careminate\Supports\Config;

dbHost = Config::get('database.host', 'localhost');
Config::set('app.debug', true);
```

---

### References & Links

* [Arr Utility Guide](#arr)
* [Collection Utility Guide](#collection)
* [Str Utility Guide](#str)
* [Macroable Utility Guide](#macroable)
* Config class reference included above.

---

**Note:**
All utilities are designed to improve development efficiency in Careminate by providing consistent, fluent, and safe operations on arrays, strings, collections, macros, and configuration.

© 2025 **Careminate Framework**  
