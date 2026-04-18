# Careminate Framework — HTTP Request + Response Abstractions

## Overview

The HTTP Request and Response layer provides object-oriented abstractions for incoming HTTP requests and outgoing HTTP responses.

This feature replaces direct dependency on PHP superglobals and raw output functions with reusable framework objects.

It forms the foundation for:
- the HTTP kernel
- middleware
- routing
- controller dispatch
- validation
- sessions
- CSRF protection
- API delivery

---

## Responsibilities

- capture incoming HTTP request data
- normalize query, post, cookie, file, and server data
- expose request metadata such as method, URL, path, headers, and IP
- provide reusable response objects
- support JSON and redirect responses
- send finalized responses to the client

---

## Core Components

### `Careminate\Http\Request`

Represents the current incoming HTTP request.

#### Features
- request capture from PHP globals
- query string access
- form input access
- cookie access
- uploaded file access
- server metadata access
- header normalization
- request URL and path helpers
- IP lookup
- data filtering helpers such as `only()` and `except()`

---

### `Careminate\Http\Response`

Represents a standard HTTP response.

#### Features
- response content storage
- status code support
- header registration
- header formatting
- response sending

---

### `Careminate\Http\JsonResponse`

Represents a JSON HTTP response.

#### Features
- JSON encoding
- automatic JSON content type header
- proper API-ready response output

---

### `Careminate\Http\RedirectResponse`

Represents an HTTP redirect response.

#### Features
- Location header support
- redirect status code handling

---

### `Careminate\Http\UploadedFile`

Represents an uploaded file.

#### Features
- original filename access
- MIME type access
- upload validation
- file move support

---

## Service Provider

### `Careminate\Foundation\Providers\HttpServiceProvider`

Registers the current request into the container as a singleton.

This ensures that the same request instance is shared across:
- controllers
- middleware
- validators
- auth services
- future kernel components

---

## Helper Functions

### `request(?string $key = null, mixed $default = null): mixed`

Returns the request object when called without arguments.  
Returns input data when a key is provided.

Examples:

```php
$request = request();
$name = request('name');
```
