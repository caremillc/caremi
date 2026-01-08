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