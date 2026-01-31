# Careminate Collection Utility Documentation

## Overview

The `Careminate\Supports\Collection` class provides a powerful and fluent wrapper around PHP arrays, similar to Laravel’s Collection system. It supports chaining, transformation, filtering, and statistical operations while integrating with the `Arr` and `Str` utility classes in the Careminate framework.

---

## Class Summary

**Namespace:** `Careminate\Supports`  
**Implements:** `ArrayAccess`, `IteratorAggregate`, `Countable`  
**Uses:** `Macroable` trait (for macro extensions)

### Core Purpose

The `Collection` class provides an expressive API for working with data arrays, including methods for mapping, filtering, sorting, aggregating, and manipulating datasets in an object-oriented and chainable way.

---

## Constructor

### `__construct(array $items = [])`
Initializes the collection with the provided items.

```php
$collection = new Collection(['a' => 1, 'b' => 2]);
```

---

## Key Methods

### `all(): array`
Returns all items as an array.

### `count(): int`
Counts the number of elements in the collection.

### `make(array $items = []): static`
Creates a new Collection instance statically.

```php
$col = Collection::make([1, 2, 3]);
```

---

## Data Transformation

### `map(callable $callback): static`
Applies a callback to each item.

```php
$col = Collection::make([1, 2, 3])->map(fn($v) => $v * 2); // [2,4,6]
```

### `filter(?callable $callback = null): static`
Filters items using a callback or removes falsy values by default.

```php
$col = Collection::make([0, 1, 2])->filter(); // [1,2]
```

### `flatten(int $depth = PHP_INT_MAX): static`
Flattens multi-dimensional arrays into a single level.

### `pluck(string|callable $key, bool $slugKeys = false): static`
Retrieves values from a key or callback, optionally slugifying the result.

### `groupBy(string|callable $key, bool $slugKeys = false): static`
Groups collection items by key or callback.

```php
$grouped = $users->groupBy('role');
```

### `keyBy(string|callable $key, bool $slugKeys = false): static`
Keys the collection by a specified key or callback.

---

## Statistical Operations

### `sum(string|callable|null $key = null): float|int`
Sums the collection values or a specific field.

### `avg(string|callable|null $key = null): float`
Calculates the average of items or field.

### `max(string|callable|null $key = null): mixed`
Returns the maximum value from the collection.

### `min(string|callable|null $key = null): mixed`
Returns the minimum value from the collection.

### `median(callable|string|null $key = null): mixed`
Calculates the median value.

---

## Sorting and Uniqueness

### `sortBy(callable|string $key, bool $ascending = true): static`
Sorts items by a key or callback.

### `sortByDesc(callable|string $key): static`
Sorts items in descending order.

### `unique(callable|string|null $key = null): static`
Removes duplicate entries by value, key, or callback.

### `reverse(): static`
Reverses the collection order.

---

## Utility and Access

### `get(string|int|null $key, mixed $default = null): mixed`
Gets an item using array dot notation.

### `set(string|int $key, mixed $value): static`
Sets an item value using array dot notation.

### `has(string|int $key): bool`
Checks if a key exists.

### `forget(string|int $key): static`
Removes a key from the collection.

### `pull(string|int $key, mixed $default = null): mixed`
Removes and returns a value.

### `push(mixed $value): static`
Appends an item.

### `collapse(): static`
Merges nested arrays or collections into one.

### `shuffle(): static`
Randomly shuffles items.

### `random(int $amount = 1): mixed`
Retrieves one or multiple random items.

---

## Functional Helpers

### `tap(callable $callback): static`
Invokes a callback with the collection, then returns it.

### `pipe(callable $callback): mixed`
Passes the collection to a callback and returns the result.

---

## Conversions

### `toJson(int $flags = 0): string`
Converts the collection to a JSON string.

### `isEmpty(): bool`
Checks if the collection is empty.

---

## Interfaces

### ArrayAccess
Allows the collection to be accessed like an array:

```php
$col = Collection::make(['name' => 'John']);
echo $col['name']; // John
```

### IteratorAggregate
Allows iteration in foreach loops.

### Countable
Allows usage of `count($collection)`.

---

## Example Usage

```php
use Careminate\Supports\Collection;

$users = Collection::make([
    ['name' => 'Alice', 'age' => 28],
    ['name' => 'Bob', 'age' => 35],
    ['name' => 'Charlie', 'age' => 28],
]);

$grouped = $users->groupBy('age');
$avgAge = $users->avg('age');
$names = $users->pluck('name')->toJson();
```

---

## Integration Notes

- Works seamlessly with `Arr` for deep key access and manipulation.
- Uses `Str` for slug operations in `groupBy` and `keyBy`.
- Fully chainable: `Collection::make($data)->filter()->map()->sortBy('id')->toJson();`

---

## Best Practices

- Use `Collection::make()` instead of manually instantiating.
- Prefer immutable-style operations (returning new instances).
- Use `tap()` for debugging chain steps.
- Combine with `Arr` for advanced data handling.

# 1 ️⃣ Data transformation pipelines (ETL / DTO processing)

Use case:
Normalize raw data from APIs, databases, or files into a clean structure.
```php 
<?php 
$users = Collection::make($rawUsers)
    ->map(fn ($user) => [
        'id'    => $user['id'],
        'email' => Str::lower($user['email']),
        'name'  => Str::title($user['name']),
    ])
    ->filter(fn ($user) => Str::contains($user['email'], '@'));
	?>
```
✔ map, filter, pluck, toArray

---

# 2️ ⃣ Business rules & domain logic

Use case:
Apply domain-specific constraints without polluting models.
```php 
<?php 
$activeUsers = $users
    ->where(fn ($u) => $u['active'])
    ->unique('email')
    ->sortBy('created_at');
?>
```
✔ where, unique, sortBy, sortByDesc
---

# 3 ️⃣ Aggregations & analytics

Use case:
Calculate metrics in memory (dashboards, reports, CLI tools).
```php 
<?php 
$revenue = Collection::make($orders)->sum('total');
$average = Collection::make($orders)->avg('total');
$max     = Collection::make($orders)->max('total');
$median  = Collection::make($orders)->median('total');
?>
```
✔ sum, avg, max, min, median
---

# 4 ⃣ Grouping & indexing datasets

Use case:
Efficient lookups and category-based processing.
```php 
<?php 
$ordersByStatus = $orders->groupBy('status');
$usersById      = $users->keyBy('id');
?>
```
✔ groupBy, keyBy
---

# 5 ️⃣ Fluent data querying (repository layer)

Use case:
Replace verbose array logic with readable, composable queries.
```php 
<?php 
$recent = $posts
    ->filter(fn ($p) => $p['published'])
    ->sortByDesc('created_at')
    ->limit(10); // via macro
?>
```
✔ filter, sortByDesc, first, last, random
---
# 6 ️⃣ UI / API response shaping

Use case:
Prepare data exactly as needed for frontend or JSON APIs.
```php 
<?php 
return $users
    ->pluck('name')
    ->values()
    ->toJson(JSON_PRETTY_PRINT);
?>
```
✔ pluck, values, toJson
---
# 7️ ⃣ Runtime extensibility via macros

Use case:
Add application-specific collection helpers without modifying core.
```php 
<?php 
Collection::macro('onlyActive', function () {
    return $this->where(fn ($item) => $item['active'] ?? false);
});

// Usage
$active = $users->onlyActive();
?>
```

✔ Macroable trait

# 8 ️⃣ Safe mutable operations (command / service layers)

Use case:
Modify collections intentionally without hidden side effects.
```php 
<?php 
$cart
    ->push($item)
    ->tap(fn ($c) => logger()->info('Item added'))
    ->set('updated_at', time());
?>
```

✔ push, tap, set, forget

# 9 ️⃣ Functional-style composition

Use case:
Compose operations as functions for clarity and testability.
```php 
<?php 
$result = $users->pipe(function (Collection $users) {
    return $users->filter(fn ($u) => $u['verified'])->count();
});
?>
```
✔ pipe, each

---
# 🔟 Console & CLI tools

Use case:
Efficient data manipulation in memory without ORM overhead.
```php 
<?php 
$topCommands = $commands
    ->groupBy('category')
    ->map(fn ($cmds) => Collection::make($cmds)->count());
?>
```
✔ groupBy, map, count

# 11 ️ ⃣ Defensive programming

Use case:
Avoid undefined index errors and brittle array access.
```php 
<?php 
$email = $userCollection->get('profile.email', 'unknown');
?>
```
✔ get, has, pull

---
# 1️2 ️⃣ Nested data flattening

Use case:
Flatten deeply nested data structures.
```php 
<?php 
$flat = $tree->flatten();
$collapsed = $collections->collapse();
?>
```
✔ flatten, collapse

---

# 1️3 ️⃣ Pagination & slicing (via macros)
```php 
<?php 
Collection::macro('paginate', function (int $perPage, int $page = 1) {
    return new static(
        array_slice($this->all(), ($page - 1) * $perPage, $perPage)
    );
});
?>
```
❌ Anti-patterns (what not to use it for)

Large datasets (>100k items) → use streaming / generators

Critical performance loops → use native arrays

Enforcing invariants → use value objects

---

🧠 Design Summary

Your Collection is ideal for:

Service layers

Repositories

API transformers

CLI tools

In-memory analytics

Framework internals

It mirrors Laravel Collections while remaining:

Framework-agnostic

Macro-extensible

Strict-typed

Testable

## Next logical enhancements

LazyCollection

reduce(), partition()

Immutable variant

Type-safe generics (PHPStan)

Benchmark suite vs native arrays

---