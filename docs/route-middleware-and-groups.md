# Careminate Framework — Route Middleware + Route Groups

## Overview

Route Middleware and Route Groups extend the Careminate router with the ability to organize routes and apply route-specific behavior.

This feature is responsible for:
- attaching middleware to individual routes
- applying shared middleware to route groups
- applying URI prefixes to grouped routes
- applying route name prefixes to grouped routes
- supporting nested route groups

This is essential for building structured applications with protected areas such as dashboards, admin panels, APIs, and role-based sections.

---

## Responsibilities

- attach middleware to routes
- execute route middleware after route matching
- support route grouping
- merge nested group prefixes
- merge nested name prefixes
- merge grouped middleware stacks
- keep route files organized and scalable

---

## Core Components

### `Careminate\Routing\Route`

Now stores:
- route middleware
- route name prefix
- final route name
- route parameters

---

### `Careminate\Routing\Router`

Now supports:
- `group(array $attributes, Closure $callback)`
- route attribute merging
- route-specific middleware execution
- nested group handling

---

## Route Middleware

Middleware can be attached directly to a route:

```php
Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware(AuthMiddleware::class)
    ->setName('profile');

This middleware only runs when that route is matched.

Route Groups

Routes can be grouped with shared attributes:

Route::group([
    'prefix' => 'dashboard',
    'as' => 'dashboard.',
    'middleware' => [AuthMiddleware::class],
], function () {
    Route::get('/', [DashboardController::class, 'index'])->setName('home');
    Route::get('/settings', [DashboardController::class, 'settings'])->setName('settings');
});

This produces:

/dashboard
/dashboard/settings

with names:

dashboard.home
dashboard.settings

and both routes share AuthMiddleware.

Nested Groups

Groups can be nested:

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/{id}', [UserController::class, 'show'])->setName('show');
    });
});

Result:

URI: /admin/users/{id}
name: admin.users.show
Middleware Execution Flow

The request lifecycle now works like this:

front controller
HTTP kernel
global middleware
router matches current route
route middleware executes
controller/closure action executes
kernel prepares response
response is sent

This keeps global concerns and route-specific concerns clearly separated.

Developer Use Cases
1. Protecting a single route

A login check can be attached to one route only.

2. Protecting a route section

A dashboard group can share authentication middleware.

3. Organizing admin routes

Admin routes can share prefixes and route names.

4. Creating scalable route files

Nested groups reduce duplication in large applications.

5. Applying business-specific route guards

Subscription, role, and account-state checks can be applied at the route level.

Benefits
keeps route files clean
avoids middleware duplication
supports scalable route organization
enables protected route sections
prepares the router for larger applications
Next Feature

The next recommended feature is:

Controller Dispatcher + Method Dependency Injection

This will allow the framework to:

inject services into controller methods
inject the current request into actions
combine route parameters and DI cleanly
improve controller ergonomics significantly

---

# What Step 7 completed

This feature is now complete. It provides:

- route middleware
- route groups
- nested groups
- URI prefixes
- name prefixes
- route-specific middleware dispatch

At this point, Careminate has a structured routing system suitable for real application development.