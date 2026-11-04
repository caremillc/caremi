# Careminate HTTP Kernel — Architecture and Contracts

**Framework:** Careminate 0.1.0-dev  
**Track:** A — Engineering Core  
**Milestone:** Day 17  
**Minimum supported PHP version:** 8.4  
**Verified development runtime:** PHP 8.5.11  
**Initial deployment model:** Conventional PHP request-per-process

## 1. Purpose

The HTTP subsystem coordinates incoming requests, route matching, middleware execution, response construction, and request lifecycle boundaries.

Its principal components are:

| Component | Responsibility |
|---|---|
| `Careminate\Http\Kernel` | Request dispatch and middleware orchestration |
| `Careminate\Http\Routing\Router` | Route registration, matching, and URL generation |
| `Careminate\Http\Request` | Validated HTTP request representation |
| `Careminate\Http\Response` | Validated HTTP response representation |
| `Careminate\Http\Middleware\Middleware` | Request/response interception contract |
| `Careminate\Foundation\Application` | Application instance used by the kernel |

## 2. Request execution flow

A normal request follows this conceptual sequence:

1. The application constructs the HTTP kernel and registers routes and middleware.
2. The kernel receives a `Request`.
3. Runtime routing configuration becomes frozen.
4. Global middleware executes in registration order.
5. The router resolves the request method and path.
6. Middleware attached to the matched route groups executes.
7. The route handler produces a `Response`.
8. Middleware unwinds in reverse order.
9. The kernel returns the final response.

A global middleware may short-circuit processing by returning a response without invoking the next handler.

Route-group middleware executes only when a matching route and method have been selected.

## 3. Request contract

The request object exposes:

- HTTP method and request target.
- Header lookup using case-insensitive names.
- Request body.
- Parsed path and raw query string.
- Matched route parameters.
- Immutable `withMethod`, `withTarget`, `withHeader`, `withBody`, and `withRouteParameters` operations.

Invalid HTTP methods, request targets, and header data are rejected during validation.

Request transformations must not mutate the original request instance.

## 4. Response contract

The response object exposes:

- HTTP status.
- Response headers.
- Response body.
- Immutable `withStatus`, `withHeader`, and `withBody` operations.

Status codes must be within the supported HTTP response range.

Response statuses that prohibit message bodies must not carry a body.

Response headers and bodies must remain isolated between requests.

## 5. Router contract

The router supports:

- HTTP method-specific registration.
- Static and dynamic path segments.
- Named routes and URL generation.
- Dynamic parameter constraints.
- Nested route groups.
- Group middleware.
- Route counting.
- Runtime configuration freezing.

### Route precedence

Matching follows deterministic precedence:

1. Exact/static matches take priority over dynamic alternatives.
2. Dynamic routes are ranked by route specificity.
3. More literal segments take priority.
4. More constrained parameters improve specificity.
5. Registration order resolves remaining ties.

This precedence policy must not change without compatibility review.

### Registration integrity

The regression suite covers:

- Duplicate method/path rejection.
- Duplicate named-route rejection.
- Invalid route and parameter definitions.
- Conflicting constraints for equivalent route patterns.
- Preservation of existing registrations after failed validation.
- Rejection of late registration after freezing.

Group state is restored when a group callback throws. Routes successfully registered before the exception may remain registered.

### HTTP error responses

- No matching route: `404 Not Found`.
- Matching path without a supported request method: `405 Method Not Allowed`.
- A 405 response exposes the applicable `Allow` header.

A failed dynamic parameter constraint does not count as a successful route match.

## 6. Middleware contract

Middleware implements:

```php
public function process(
    Request $request,
    Closure $next,
): Response;
```

The middleware pipeline supports:

- Global middleware.
- Route-group middleware.
- Nested middleware.
- Short-circuit responses.
- Request transformation.
- Response transformation.
- Exception propagation.

### Ordering

For an outer and inner middleware, the execution order is:

```text
outer: before
  inner: before
    route handler
  inner: after
outer: after
```

When a middleware returns without calling `$next`, downstream middleware and the handler do not execute.

### Security boundary

The kernel guarantees middleware execution mechanics, not an application-specific authentication policy.

Authentication, authorization, CSRF protection, and other security policies require correctly implemented and configured middleware.

A protected handler must not execute when its guard returns a denial response.

## 7. Exceptions and recovery

Handler and middleware exceptions propagate to the caller unless explicitly handled by middleware or another application layer.

The kernel's tested behavior permits subsequent requests after an exception.

Repeated failures must not leave the kernel in a permanently locked dispatch state.

Middleware that allocates resources should use `try/finally` when cleanup is required.

## 8. Lifecycle invariants

After the first handled request:

- Route configuration is frozen.
- Late route registration is rejected.
- Previously registered routes remain dispatchable.
- Middleware configuration must remain consistent with the frozen execution pipeline.
- Independent kernel instances maintain separate routing and middleware state.

The tested execution model is sequential and in-process.

Concurrent dispatch and long-running worker support are not established by these tests.

## 9. Verification baseline

At Day 17, Increment 11:

| Metric | Verified result |
|---|---|
| PHPUnit tests | 599 passed |
| Assertions | 2,835 |
| Smoke test | Passed |
| Benchmark scenarios | 11 completed |
| PHP runtime | 8.5.11 |
| Framework version | 0.1.0-dev |

The tests cover correctness and bounded repeated-request resilience.

They do not prove unrestricted concurrency safety, production throughput, or indefinite memory stability.

## 10. Compatibility requirements

Changes to the HTTP subsystem must preserve:

- Existing public method signatures.
- Request and response immutability.
- Route precedence.
- Named-route URL generation.
- Middleware execution order.
- Exception propagation.
- Route freeze behavior.
- HTTP error semantics.

Any intentional change to these contracts requires corresponding regression tests, documentation updates, and a compatibility assessment.

## 11. Scope exclusions

The Day 17 HTTP milestone does not establish:

- Production-ready authentication or authorization providers.
- Concurrent kernel dispatch.
- Long-running worker lifecycle support.
- Production web-server throughput.
- Network latency characteristics.
- Complete HTTP protocol compliance.
- A full security audit.

These capabilities require separate implementation and verification milestones.
