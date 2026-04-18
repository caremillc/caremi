# Careminate Framework — HTTP Kernel

## Overview

The HTTP Kernel is the central coordinator of the request lifecycle in Careminate.

It is responsible for:
- receiving the current request
- passing the request through global middleware
- dispatching the request to the application entry point
- normalizing the result into a response
- sending the final response
- running termination hooks after response delivery

This layer is the runtime bridge between the application container, middleware, routing, and response emission.

---

## Responsibilities

- handle the current HTTP request
- maintain a global middleware stack
- pass requests through the middleware pipeline
- dispatch requests to the application layer
- normalize handler results into response objects
- catch and render exceptions
- terminate middleware after response delivery

---

## Core Components

### `Careminate\Contracts\Http\KernelInterface`

Defines the HTTP kernel contract.

#### Methods
- `handle(RequestInterface $request): ResponseInterface`
- `send(RequestInterface $request): void`
- `terminate(RequestInterface $request, ResponseInterface $response): void`

---

### `Careminate\Contracts\Http\MiddlewareInterface`

Defines the structure of middleware handlers.

#### Method
- `handle(RequestInterface $request, Closure $next): mixed`

---

### `Careminate\Http\Pipeline`

Executes middleware sequentially and passes control to the final destination callback.

#### Responsibilities
- receive the request
- iterate through middleware
- resolve middleware through the container
- call each middleware’s `handle()` method
- pass control to the destination callback

---

### `Careminate\Http\Kernel`

The main HTTP kernel implementation.

#### Responsibilities
- store global middleware
- bind current request into the container
- execute middleware pipeline
- dispatch to the application layer
- normalize response types
- catch exceptions
- send responses
- run middleware termination hooks

---

### `Careminate\Foundation\Providers\KernelServiceProvider`

Registers the HTTP kernel as a singleton in the service container.

---

## Middleware Lifecycle

Each middleware receives:

- the current request
- a `$next` callback

Example:

```php
public function handle(RequestInterface $request, Closure $next): mixed
{
    return $next($request);
}
```
---


Middleware can:
1.continue the request
2.short-circuit and return a response
3.inspect request data
4.modify the outgoing response

Middleware can also optionally define:
<?php
public function terminate(RequestInterface $request, ResponseInterface $response): void
{
    // post-response logic
}

?>

Response Normalization
The kernel converts handler return values into proper response objects.
Supported return values
String
PHP
return 'Hello';
Array / object
PHP
return ['ok' => true];
Response object
PHP
return response('Done');
Null
PHP
return null;
Normalization behavior
strings become Response
arrays and objects become JsonResponse
null becomes an empty 204 No Content response
existing ResponseInterface objects pass through unchanged
Exception Handling
If an exception is thrown during request handling:
in debug mode, a detailed plain-text error response is returned
in production mode, a generic 500 Server Error response is returned
This is a minimal exception rendering foundation before a dedicated exception handler is introduced.
Bootstrap Example
<?php
$app->register(ConfigServiceProvider::class);
$app->register(HttpServiceProvider::class);
$app->register(KernelServiceProvider::class);

$kernel = $app->make(KernelInterface::class);
$kernel->setMiddleware([
    TrustProxies::class,
]);

?>

Front Controller Example
<?php
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(KernelInterface::class);
$request = $app->make(RequestInterface::class);

$kernel->send($request);

?>

Developer Use Cases
1. Centralized HTTP orchestration
The front controller stays minimal while the kernel coordinates request handling.
2. Global middleware registration
All requests pass through a consistent middleware stack.
3. Flexible handler output
Controllers and route handlers can return strings, arrays, or response objects.
4. Controlled error rendering
Exceptions are converted into valid HTTP responses.
5. Termination hooks
Middleware can run post-response logic for logging, session saving, and metrics.
Benefits
centralizes the HTTP request lifecycle
standardizes middleware execution
simplifies front controller logic
normalizes handler output into responses
prepares the framework for routing and controller dispatch
provides a stable base for future web features
Next Feature
The next recommended feature is:
Router + Route Dispatcher
This will allow the framework to:
register GET, POST, PUT, PATCH, DELETE routes
match incoming requests
dispatch closures and controllers
support named routes and route parameters
integrate route execution into the kernel

---

# What Step 5 completed

This feature is now complete. It provides:

- kernel contract
- middleware contract
- middleware pipeline
- request lifecycle orchestration
- response normalization
- termination hooks
- router integration seam

That is the full HTTP runtime backbone needed before routing.

## Next step

The correct next feature is:

### Step 6 — Router + Route Dispatcher

That will include:

- route definition API
- route collection
- route matching
- parameter extraction
- controller and closure dispatch
- named routes
- router service provider
- kernel integration
- full Markdown documentation
