# Careminate Support - String Utilities (`Str` Class)

The `Careminate\Support\Str` class offers a robust set of static utility methods for string manipulation.  
It is built with performance, multibyte safety, and readability in mind—making it an essential part of the **Careminate Framework** core utilities.

---

## 📘 Overview

**Namespace:** `Careminate\Support`  
**Class:** `Str`  
**Type:** Static Utility Class

---

## 🧩 Key Features

- Case conversion (camel, snake, kebab, title, upper, lower)
- String containment and position checks
- Trimming and limiting utilities
- Slug and transliteration generation
- Random string generation
- Unicode-safe operations via `mb_*` functions

---

## ⚙️ Methods and Use Cases

### 1. `camel(string $value): string`
Converts a string to **camelCase** format.

```php
Str::camel('user_name'); // 'userName'
Str::camel('User-name'); // 'userName'
```

---

### 2. `snake(string $value, string $delimiter = '_'): string`
Converts a string to **snake_case**.

```php
Str::snake('UserName'); // 'user_name'
Str::snake('UserName', '-'); // 'user-name'
```

---

### 3. `kebab(string $value): string`
Converts a string to **kebab-case** (alias for snake with dash).

```php
Str::kebab('UserName'); // 'user-name'
```

---

### 4. `title(string $value): string`
Converts to **Title Case**.

```php
Str::title('user_name'); // 'User Name'
Str::title('hello-world'); // 'Hello World'
```

---

### 5. `lower(string $value): string`
Converts all characters to lowercase (multibyte safe).

```php
Str::lower('HeLLo'); // 'hello'
```

---

### 6. `upper(string $value): string`
Converts all characters to uppercase (multibyte safe).

```php
Str::upper('hello'); // 'HELLO'
```

---

### 7. `limit(string $value, int $limit = 100, string $end = '...'): string`
Truncates a string to a specified length, adding a suffix if truncated.

```php
Str::limit('The quick brown fox jumps over the lazy dog', 20);
// 'The quick brown fox...'
```

---

### 8. `contains(string $haystack, string|array $needles): bool`
Checks if a string contains one or more substrings.

```php
Str::contains('Hello world', 'world'); // true
Str::contains('Hello world', ['foo', 'world']); // true
```

---

### 9. `startsWith(string $haystack, string|array $needles): bool`
Determines if a string starts with one or more substrings.

```php
Str::startsWith('framework', 'frame'); // true
```

---

### 10. `endsWith(string $haystack, string|array $needles): bool`
Determines if a string ends with one or more substrings.

```php
Str::endsWith('framework', 'work'); // true
```

---

### 11. `after(string $subject, string $search): string`
Returns the substring after the first occurrence of a given value.

```php
Str::after('email@example.com', '@'); // 'example.com'
```

---

### 12. `before(string $subject, string $search): string`
Returns the substring before the first occurrence of a given value.

```php
Str::before('email@example.com', '@'); // 'email'
```

---

### 13. `random(int $length = 16): string`
Generates a cryptographically secure random string of the given length.

```php
Str::random(8); // e.g., 'f3a1b7c9'
```

---

### 14. `slug(string $title, string $separator = '-'): string`
Creates a simple URL-friendly slug from a string.

```php
Str::slug('Hello World! Welcome'); // 'hello-world-welcome'
```

---

### 15. `slugify(string $text, array $opts = []): string`
An **advanced slug generator** with options for transliteration, limits, lowercase, locale, and ASCII filtering.

```php
Str::slugify('Déjà Vu! – PHP Framework', [
    'separator' => '-',
    'limit' => 20,
    'lowercase' => true,
    'transliterate' => true,
]);
// 'deja-vu-php-framework'
```

---

## 🧠 Best Practices

- Use `Str::camel`, `Str::snake`, and `Str::kebab` for consistent naming conventions in your framework or database keys.
- Prefer `Str::slugify` over `Str::slug` for multilingual or SEO-friendly URL generation.
- Use `Str::limit` for safely truncating text in UI or reports.
- Combine `Str::contains`, `Str::startsWith`, and `Str::endsWith` for powerful conditional checks in validation or filtering logic.

---

## 🧪 Example: Real-World Use

```php
$title = 'Learning PHP with Careminate Framework';

$slug = Str::slugify($title);  // 'learning-php-with-careminate-framework'

if (Str::contains($slug, 'careminate')) {
    echo Str::upper("Welcome to $slug!");
}
// Output: WELCOME TO LEARNING-PHP-WITH-CAREMINATE-FRAMEWORK!
```

---

## 📚 Summary Table

| Method | Description |
|--------|--------------|
| camel | Convert to camelCase |
| snake | Convert to snake_case |
| kebab | Convert to kebab-case |
| title | Convert to Title Case |
| lower | Convert to lowercase |
| upper | Convert to uppercase |
| limit | Truncate string with suffix |
| contains | Check if string contains substring |
| startsWith | Check if string starts with value |
| endsWith | Check if string ends with value |
| after | Return substring after value |
| before | Return substring before value |
| random | Generate random string |
| slug | Create URL-friendly slug |
| slugify | Advanced slug generator with transliteration |

---

## 🧾 Notes

- All string methods are **multibyte-safe**, using `mb_*` functions.
- `slugify()` supports transliteration via the **intl** or **iconv** extension.
- `random()` uses `random_bytes()` for cryptographic safety.

---

© 2025 **Careminate Framework**  
Utility maintained under `Careminate\Support\Str`