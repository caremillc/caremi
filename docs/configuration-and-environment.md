# Careminate Framework — Configuration Repository + Environment Loader

## Overview

The Configuration Repository and Environment Loader provide the framework with a centralized, environment-aware runtime configuration system.

This feature is responsible for:
- loading environment variables from `.env`
- loading configuration arrays from `config/*.php`
- exposing `env()` and `config()` helpers
- supporting dot notation access to nested configuration
- providing a shared singleton configuration repository

This system is foundational for database, cache, session, mail, validation, and future package/module configuration.

---

## Responsibilities

- load `.env` key-value pairs into runtime memory
- load PHP config files from the `config` directory
- expose a shared repository of configuration data
- allow dot notation retrieval of nested values
- provide safe defaults when values are missing

---

## Core Components

### `Careminate\Environment\EnvLoader`

Loads environment values from a `.env` file and populates:
- `$_ENV`
- `$_SERVER`
- process environment via `putenv()`

#### Supported conversions
- `true` → `true`
- `false` → `false`
- `null` → `null`
- `empty` → `''`

---

### `Careminate\Config\Loader`

Loads all `*.php` files from the config directory.

Each config file must return an array.

Example:

```php
return [
    'name' => env('APP_NAME', 'Careminate'),
];
```
---

# What Step 3 completed

This feature is now complete. It provides:

- env loading
- config loading
- repository access
- helper APIs
- container integration
- a stable base for infrastructure services

---

# Next step

The correct next feature is:

## Step 4 — HTTP Request + Response Abstractions

That will include:

- `Request` class
- `Response` class
- JSON response
- redirect response foundation
- header handling
- query/body/file access
- server/request normalization
- use cases
- markdown documentation

