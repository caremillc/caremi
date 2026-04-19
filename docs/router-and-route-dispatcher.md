# Careminate Framework — Router + Route Dispatcher

## Overview

The Router and Route Dispatcher give Careminate the ability to map incoming HTTP requests to executable handlers.

This feature is responsible for:
- registering routes
- matching routes by HTTP method and URI
- extracting route parameters
- dispatching closures and controller actions
- generating named route URLs
- integrating route handling into the HTTP kernel

This is the entry point where the framework becomes capable of serving real application pages and endpoints.

---

## Responsibilities

- define routes for supported HTTP verbs
- store registered routes
- match the current request to a route
- extract dynamic route parameters
- dispatch closures and controller actions
- maintain the current matched route
- support named routes and URL generation

---

## Core Components

### `Careminate\Routing\Route`

Represents a single route definition.

#### Stores
- HTTP methods
- URI pattern
- action
- route name
- extracted parameters

---

### `Careminate\Routing\RouteCollection`

Stores all registered routes and performs route lookup.

#### Responsibilities
- add routes
- return all routes
- match route by method and path
- find route by name
- check named route existence

---

### `Careminate\Routing\RouteDispatcher`

Executes the matched route action.

#### Supported action types
- closures
- `"Controller@method"` strings
- `[Controller::class, 'method']` arrays

---

### `Careminate\Routing\Router`

Main routing service.

#### Responsibilities
- register routes
- dispatch request to matched route
- track current route
- generate route URLs by name

---

### `Careminate\Foundation\Providers\RoutingServiceProvider`

Registers routing services into the container and loads route definition files.

---

## Route Definition Examples

### Closure route

```php
Route::get('/ping', function () {
    return ['pong' => true];
});
Controller route
Route::get('/users/{id}', [UserController::class, 'show']);
Named route
Route::get('/dashboard', [DashboardController::class, 'index'])->setName('dashboard');
Supported HTTP Verbs
Route::get()

Route::post()

Route::put()

Route::patch()

Route::delete()

Route::any()

Route Parameters
Dynamic route segments use curly braces:

Route::get('/users/{id}', [UserController::class, 'show']);
For a request to:

/users/42
the dispatcher passes:

show('42')
Named Routes
Routes can be named and later resolved into URLs.

Define
Route::get('/users/{id}', [UserController::class, 'show'])->setName('users.show');
Generate URL
$url = route('users.show', ['id' => 42]);
Result:

/users/42
Kernel Integration
The router integrates with the kernel through a container dispatch hook.

The kernel calls the router when router.dispatch is registered.

This keeps the kernel decoupled from the routing implementation while still allowing the full HTTP lifecycle to function correctly.

Developer Use Cases
1. Basic page routes
Developers can quickly register static pages or simple endpoints.

2. Controller-based architecture
Business logic can move into controllers while the router stays thin.

3. Dynamic resource URLs
Applications can serve users, posts, projects, and other resources through parameterized routes.

4. Named route generation
Links and redirects can be built from route names rather than hardcoded paths.

5. API-friendly closures
Simple JSON endpoints can be defined quickly using closures.

Benefits
provides real URL-to-handler mapping

supports clean controller dispatch

enables dynamic route parameters

improves developer ergonomics with named routes

integrates naturally with the HTTP kernel

prepares the framework for route middleware and groups

Next Feature
The next recommended feature is:

Route Middleware + Route Groups

This will allow the framework to:

attach middleware to specific routes

group routes under shared middleware

apply URI prefixes

support name prefixes

organize larger applications cleanly


---

# What Step 6 completed

This feature is now complete. It provides:

- route registration
- route matching
- controller dispatch
- closure dispatch
- named routes
- route parameters
- kernel integration

At this point, Careminate has a full basic HTTP request lifecycle:

- request capture
- kernel orchestration
- middleware pipeline
- router dispatch
- response normalization
- response sending

## Next step

The correct next feature is:

### Step 7 — Route Middleware + Route Groups

That will include:

- per-route middleware
- grouped middleware
- URI prefixes
- route name prefixes
- nested groups
- cleaner route organization
- Markdown documentation