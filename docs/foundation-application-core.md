# Careminate Framework — Foundation / Application Core

## Overview

The Foundation / Application Core is the central bootstrap feature of the Careminate Framework.  
It is responsible for initializing the framework, managing core paths, registering service providers, and coordinating the boot lifecycle.

This layer is the root of the framework runtime and is required before routing, middleware, views, database, or authentication can function.

---

## Responsibilities

- Maintain the framework base path
- Provide helper access to framework directories
- Manage the environment path and environment file
- Register service providers
- Execute provider boot logic
- Expose a globally accessible application instance

---

## Core Classes

### `Careminate\Foundation\Application`

The main runtime object of the framework.

#### Responsibilities
- stores base path
- exposes directory path methods
- registers service providers
- boots the application
- keeps a shared global application instance

---

### `Careminate\Foundation\ServiceProvider`

Base class for all framework and application service providers.

#### Responsibilities
- define service registration logic
- define boot logic after registration phase

---

## Available Path Helpers

### `base_path($path = '')`
Returns the root project path.

### `app_path($path = '')`
Returns the `app/` path.

### `bootstrap_path($path = '')`
Returns the `bootstrap/` path.

### `config_path($path = '')`
Returns the `config/` path.

### `public_path($path = '')`
Returns the `public/` path.

### `storage_path($path = '')`
Returns the `storage/` path.

### `app()`
Returns the globally shared application instance.

---

## Example Bootstrapping

### `bootstrap/app.php`

```php
<?php

use Careminate\Foundation\Application;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../framework/src/Support/Helpers/functions.php';

$app = new Application(dirname(__DIR__));
$app->boot();

return $app;

?>
``` 

---
# What comes next

The correct next feature is:

## Step 2 — Service Container (IoC - Dependency Injection)

That feature will give you:

- `bind()`
- `singleton()`
- `instance()`
- `make()`
- auto constructor injection
- interface-to-class resolution
- contextual binding foundation
- container contracts compatible with the rest of the framework

That will unlock the HTTP kernel, router, controllers, config repository, and database services.

---
