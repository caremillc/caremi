# Careminate Macroable Trait Documentation

## Overview

The `Macroable` trait provides a dynamic way to add new methods (called **macros**) to a class at runtime. 
It allows developers to extend the functionality of Careminate components such as `Collection`, `Str`, or `Request` without directly modifying their source code.

This design pattern encourages flexibility, reusability, and maintainability across your framework.

---

## Namespace
```php
namespace Careminate\Support;
```

## Trait Definition
```php
trait Macroable
{
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void;
    public static function hasMacro(string $name): bool;
    public function __call(string $method, array $parameters);
    public static function __callStatic(string $method, array $parameters);
}
```

---

## Core Methods

### `macro(string $name, callable $macro): void`
Registers a macro (a dynamic method) for the class.

**Example:**
```php
Str::macro('reverse', function (string $value) {
    return strrev($value);
});
```

---

### `hasMacro(string $name): bool`
Checks if a macro with the given name has been defined.

**Example:**
```php
if (Str::hasMacro('reverse')) {
    echo "Reverse macro is available.";
}
```

---

### `__call(string $method, array $parameters)`
Handles instance-level dynamic macro calls.

**Example:**
```php
$collection = new Collection([1, 2, 3]);
Collection::macro('sumSquare', function () {
    return array_sum(array_map(fn($i) => $i * $i, $this->all()));
});

echo $collection->sumSquare(); // Output: 14
```

---

### `__callStatic(string $method, array $parameters)`
Handles static macro calls.

**Example:**
```php
Str::macro('shout', fn(string $text) => strtoupper($text . '!'));
echo Str::shout('hello'); // Output: HELLO!
```

---

## Use Cases

### 1. Extending Collection Dynamically
```php
use Careminate\Support\Collection;

Collection::macro('sumSquare', function () {
    return array_sum(array_map(fn($i) => $i * $i, $this->all()));
});

$numbers = new Collection([1, 2, 3, 4]);
echo $numbers->sumSquare(); // Output: 30
```

### 2. Extending String Utilities
```php
use Careminate\Support\Str;

Str::macro('reverse', fn(string $value) => strrev($value));
echo Str::reverse('Careminate'); // Output: etanimeraC
```

### 3. Creating Static Utility Macros
```php
use Careminate\Support\Str;

Str::macro('shout', fn(string $text) => strtoupper($text . '!'));
echo Str::shout('hello'); // Output: HELLO!
```

---

## Best Practices

✅ Register macros inside **service providers** or a **bootstrap file** for consistent loading.  
✅ Always check with `hasMacro()` before defining a macro to prevent overwriting.  
✅ Use macros to extend **framework behavior** rather than modifying the core classes directly.  

---

## Integration Example in Careminate

You can register macros globally in a `boot` method of your Service Provider:

```php
namespace App\Providers;

use Careminate\Support\Collection;
use Careminate\Support\Str;

class MacroServiceProvider
{
    public function boot(): void
    {
        Collection::macro('sumSquare', function () {
            return array_sum(array_map(fn($i) => $i * $i, $this->all()));
        });

        Str::macro('slugifyAndUpper', fn(string $text) => strtoupper(Str::slug($text)));
    }
}
```

Then, add the provider to your configuration (e.g., `config/app.php`) so the macros load automatically.

---

## Conclusion

The `Macroable` trait empowers Careminate developers to extend framework functionality easily and dynamically. 
It promotes maintainability by separating enhancements from the core system, following the **open/closed principle** — 
classes should be open for extension but closed for modification.

---

© 2025 **Careminate Framework**  
Utility maintained under `Careminate\Support\Macroable` 