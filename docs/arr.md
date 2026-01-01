# Careminate Support - Array Utilities (`Arr` Class)

The `Careminate\Support\Arr` class provides a collection of powerful, static utility methods for working with arrays in PHP.  
It is designed to simplify array manipulation, similar to Laravel's `Arr` helper, with additional safety, readability, and flexibility features.

---

## 📘 Overview

**Namespace:** `Careminate\Support`  
**Class:** `Arr`  
**Type:** Static Utility Class

---

## 🧩 Key Features

- Dot-notation access to nested array elements.
- Safe getting and setting of array values.
- Filtering, mapping, and flattening helpers.
- Random and shuffle support.
- Compatible with `ArrayAccess` objects.
- Fully type-safe with `strict_types` enabled.

---

## ⚙️ Methods and Use Cases

### 1. `add(array $array, string|int $key, mixed $value): array`
Adds a key-value pair only if the key does not exist.

```php
$array = ['name' => 'John'];
$result = Arr::add($array, 'age', 30);
// ['name' => 'John', 'age' => 30]
```

---

### 2. `only(array $array, array|string $keys): array`
Returns only the specified keys.

```php
$user = ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'];
$result = Arr::only($user, ['id', 'name']);
// ['id' => 1, 'name' => 'Alice']
```

---

### 3. `exists(array|ArrayAccess $array, string|int $key): bool`
Checks if a key exists in an array or `ArrayAccess` object.

```php
Arr::exists(['a' => 1], 'a'); // true
```

---

### 4. `get(array|ArrayAccess $array, string|int|null $key, mixed $default = null): mixed`
Retrieves a value using dot notation.

```php
$data = ['user' => ['profile' => ['name' => 'Tom']]];
$name = Arr::get($data, 'user.profile.name'); // 'Tom'
```

---

### 5. `set(array &$array, string|int $key, mixed $value): void`
Sets a value in an array using dot notation.

```php
Arr::set($config, 'app.debug', true);
```

---

### 6. `has(array $array, string|int $key): bool`
Checks if a nested key exists.

```php
Arr::has(['user' => ['id' => 1]], 'user.id'); // true
```

---

### 7. `forget(array &$array, string|int $key): void`
Removes a value from an array using dot notation.

```php
Arr::forget($user, 'profile.address');
```

---

### 8. `first(array $array, ?callable $callback = null, mixed $default = null): mixed`
Gets the first element optionally matching a condition.

```php
Arr::first([1, 2, 3], fn($v) => $v > 1); // 2
```

---

### 9. `last(array $array, ?callable $callback = null, mixed $default = null): mixed`
Gets the last element optionally matching a condition.

```php
Arr::last([1, 2, 3], fn($v) => $v < 3); // 2
```

---

### 10. `flatten(array $array, int $depth = PHP_INT_MAX): array`
Flattens a multidimensional array to a single level.

```php
Arr::flatten([1, [2, [3]]]); // [1, 2, 3]
```

---

### 11. `pluck(array $array, string $value, ?string $key = null): array`
Extracts values from an array of arrays or objects.

```php
$users = [
    ['id' => 1, 'name' => 'John'],
    ['id' => 2, 'name' => 'Jane']
];
Arr::pluck($users, 'name'); // ['John', 'Jane']
```

---

### 12. `map(array $array, callable $callback): array`
Applies a callback to all items.

```php
Arr::map(['a' => 1, 'b' => 2], fn($v, $k) => $v * 2);
// ['a' => 2, 'b' => 4]
```

---

### 13. `where(array $array, callable $callback): array`
Filters items using a callback.

```php
Arr::where([1, 2, 3, 4], fn($v) => $v > 2);
// [3, 4]
```

---

### 14. `random(array $array, int $number = 1): mixed`
Retrieves one or more random elements.

```php
Arr::random(['apple', 'banana', 'cherry']);
```

---

### 15. `shuffle(array $array): array`
Randomly shuffles an array.

```php
Arr::shuffle([1, 2, 3]);
```

---

### 16. `collapse(array $array): array`
Flattens an array of arrays into a single array.

```php
Arr::collapse([[1, 2], [3, 4]]);
// [1, 2, 3, 4]
```

---

### 17. `pull(array &$array, string|int $key, mixed $default = null): mixed`
Gets and removes a key from an array.

```php
$user = ['name' => 'John', 'age' => 25];
$name = Arr::pull($user, 'name');
// $name = 'John', $user = ['age' => 25]
```

---

### 18. `wrap(mixed $value): array`
Wraps a value in an array if it’s not already one.

```php
Arr::wrap('x'); // ['x']
Arr::wrap(['x']); // ['x']
```

---

### 19. `accessible(mixed $value): bool`
Determines if the value can be accessed like an array.

```php
Arr::accessible(['a' => 1]); // true
```

---

### 20. `except(array $array, array|string $keys): array`
Removes specified keys and returns the rest.

```php
$user = ['id' => 1, 'name' => 'Jane', 'email' => 'jane@example.com'];
Arr::except($user, ['email']);
// ['id' => 1, 'name' => 'Jane']
```

---

## 🧠 Best Practices

- Use **dot notation** for nested data access.
- Prefer `Arr::get()` and `Arr::set()` for safety over direct array access.
- Use `Arr::where()` for clean filtering logic.
- Combine `pluck()` and `map()` for efficient data transformation.

---

## 🧪 Example: Deep Usage

```php
$config = [
    'database' => [
        'connections' => [
            'mysql' => ['host' => '127.0.0.1', 'port' => 3306]
        ]
    ]
];

// Get nested value
$host = Arr::get($config, 'database.connections.mysql.host'); // 127.0.0.1

// Change nested value
Arr::set($config, 'database.connections.mysql.port', 3307);

// Remove a key
Arr::forget($config, 'database.connections.mysql.host');
```

---

## 📚 Summary

| Method | Description |
|--------|--------------|
| add | Add key-value pair if not exists |
| only | Return only specified keys |
| exists | Check if a key exists |
| get | Retrieve value with dot notation |
| set | Set value with dot notation |
| has | Check if nested key exists |
| forget | Remove value by key |
| first / last | Get first or last element optionally via callback |
| flatten | Flatten nested arrays |
| pluck | Extract values by key |
| map | Apply callback to elements |
| where | Filter array elements |
| random | Get random items |
| shuffle | Randomize order |
| collapse | Merge array of arrays |
| pull | Get and remove item |
| wrap | Wrap value into array |
| accessible | Check array accessibility |
| except | Remove specific keys |

---

© 2025 **Careminate Framework**  
Utility maintained under `Careminate\Support\Arr`