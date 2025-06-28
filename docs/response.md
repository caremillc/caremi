# HTTP Response System

## Overview

The `Careminate\Http\Responses\Response` class provides a powerful and expressive way to return responses from your application. It supports various content types, HTTP status codes, headers, redirections, and output buffering.

All responses are handled using either the `Response` or `RedirectResponse` class.

---

## Creating a Basic Response

```php
use Careminate\Http\Responses\Response;

$response = new Response('Hello World!');
$response->send();
```

# Setting Status Codes

## You can set the status code using the constructor or fluent setStatus():

```php
$response = new Response('Created', Response::HTTP_CREATED);
```
# or

```php
$response->setStatus(Response::HTTP_NO_CONTENT);
```

# Setting Headers

## Set headers using:
```php
$response->setHeader('X-Framework', 'Careminate');
$response->setHeaders([
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'application/json',
]);
```

# To retrieve or remove headers:
```php
$value = $response->getHeader('X-Framework');
$response->removeHeader('Cache-Control');
```

# JSON Responses

## Return JSON data with:
```php
$response = (new Response())->json([
    'success' => true,
    'message' => 'Data retrieved successfully',
]);
$response->send();
```

# You may configure global JSON encoding behavior:
```php
Response::setJsonOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
```

# HTML & Text Responses
## HTML:
```php
$response = Response::html('<h1>Welcome</h1>');
```

# Plain Text:
```php
$response = Response::text('Plain text response');
```

# Both accept optional status and headers:
```php
Response::html('<p>Thanks</p>', Response::HTTP_ACCEPTED, ['X-Custom' => 'Yes']);
```

# Redirection

## Use the redirect() method to redirect:
```php
$response = (new Response())->redirect('/login');
$response->send();
```

# Or use the dedicated RedirectResponse:
```php
use Careminate\Http\Responses\RedirectResponse;

$response = new RedirectResponse('/dashboard');
$response->send();
```

# You can control behavior:
```php 
$response->setExitAfterRedirect(false); // do not exit after send()
$response = $response->withUrl('/profile');
``` 

# Common Error Helpers

## Use static helpers for common errors:
```php 
return Response::notFound();             // 404 Not Found
return Response::badRequest();           // 400 Bad Request
return Response::unauthorized();         // 401 Unauthorized
return Response::forbidden();            // 403 Forbidden
return Response::serverError();          // 500 Internal Server Error
return Response::noContent();            // 204 No Content

```
## All helpers allow optional messages or headers.

# Status Checking Methods

## These allow conditional logic:
```php 
$response->isSuccessful();    // true if 2xx
$response->isRedirection();   // true if 3xx
$response->isClientError();   // true if 4xx
$response->isServerError();   // true if 5xx
```

# Output Buffering

## Output buffering is enabled by default to capture exceptions during rendering.

## You can disable it if needed:
```php 
$response->setOutputBuffering(false);
```

# Example (from public/index.php)
```php 

use Careminate\Http\Responses\Response;

$response = new Response('Hello World! from index');

$response->setHeader('X-Powered-By', 'Careminate');
$response->send();

```
# Internals

    Response manages status, headers, content, and output behavior

    RedirectResponse extends Response and adds validation, exit() logic

    Status text mapping is stored in HTTP_STATUS_TEXTS

    Default charset is UTF-8 for all content types



    | Feature          | Method                                        |
| ---------------- | --------------------------------------------- |
| Set content      | `setContent(string)`                          |
| Set status       | `setStatus(int)` / `withStatus(int)`          |
| Set headers      | `setHeader()`, `setHeaders()`, `withHeader()` |
| Send response    | `send()`                                      |
| JSON response    | `json(array)`                                 |
| Text/HTML        | `Response::text()`, `Response::html()`        |
| Redirect         | `redirect(string)` / `RedirectResponse`       |
| Output buffering | `setOutputBuffering(bool)`                    |

