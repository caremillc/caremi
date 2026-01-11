# 🗂️ Careminate Framework — Project Structure & File Tree Guide

This section expands on the core bootstrap documentation by providing a **complete file and folder layout** for the Careminate framework.  
It shows where each component fits and how the framework evolves from a simple bootstrap to a modular architecture.

---

## 🧱 Root Project Structure
pending ... structure need verification
```
careminate/
├── app/
│   ├── Exceptions/
│   │   ├── Handler.php
│   │   └── AuthException.php
│   ├── Http/
│   │   ├── Kernel.php
│   │   ├── Controllers/
│   │   │   └── HomeController.php
│   │   └── Middleware/
│   │       └── Authenticate.php
│   ├── Models/
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/
│   ├── app.php
│   └── performance.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   └── logging.php
│
├── public/
│   ├── index.php
│   └── .htaccess
│
├── resources/
│   ├── views/
│   │   └── welcome.caremi.php
│   ├── lang/
│   │   ├── en/
│   │   │   └── messages.php
│   │   └── ar/
│   │       └── messages.php
│   └── assets/
│       └── css/
│           └── app.css
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
│   ├── cache/
│   ├── logs/
│   │   └── careminate.log
│   ├── sessions/
│   └── views/
│
├── vendor/
│   └── autoload.php
│
├── .env
├── composer.json
├── caremi (CLI tool)
└── README.md
```

---

## Use Case 1: Basic HTTP Request Handling

# Scenario: Normal application flow without exceptions
```php

// Client makes a GET request to "/"
// The Kernel handles it successfully
$request = Request::createFromGlobals(); // Path: "/", Method: "GET"
$kernel = new Kernel();
$response = $kernel->handle($request); // Returns HTML response
$response->send(); // Sends "Hello World from Kernel" to client
```
---

## Expected Output: HTML page with request details
Status Code: 200 OK
Use Case 2: Authentication Exception (401)

# Scenario: User tries to access protected resource without credentials
```php

// In a controller or middleware
throw new AuthException('Authentication required');

// Or with custom message
throw new AuthException('Invalid API token provided', 401);
```

# Expected Output (Debug mode ON):

    JSON: {"error": {"code": 401, "message": "Invalid API token provided", "type": "AuthException", "details": {...}}}

    HTML: Detailed error page with stack trace

# Expected Output (Debug mode OFF):

    JSON: {"error": {"code": 401, "message": "Unauthorized", "type": "AuthException"}}

    HTML: User-friendly "401 Unauthorized" page with gradient background

## Use Case 3: Validation Exception (422)

# Scenario: Form submission fails validation
```php

// Assuming ValidationException exists with getErrors() method
// In a validation service or controller
throw new ValidationException('Input validation failed', [
    'email' => ['The email must be a valid email address'],
    'password' => ['The password must be at least 8 characters']
]);
```
---

## Expected Output (JSON):
```json

{
    "error": {
        "code": 422,
        "message": "Validation failed",
        "type": "ValidationException",
        "errors": {
            "email": ["The email must be a valid email address"],
            "password": ["The password must be at least 8 characters"]
        }
    }
}
```
---

## Use Case 4: Invalid Argument Exception (400)

# Scenario: Client sends malformed data
```php

// In a service or controller
function processOrder(array $data) {
    if (!isset($data['items']) || empty($data['items'])) {
        throw new \InvalidArgumentException('Order must contain at least one item');
    }
    // Process order...
}
```
---

## Expected Output (Debug mode ON):

    JSON with full error details including file and line

    HTML with stack trace

## Expected Output (Debug mode OFF):

    JSON: {"error": {"code": 400, "message": "An error occurred", "type": "InvalidArgumentException"}}

    HTML: "400 Bad Request" production page

## Use Case 5: AJAX/JSON Request Errors

# Scenario: JavaScript application makes API request
```php

// Client sends: Header: Accept: application/json
// Or: X-Requested-With: XMLHttpRequest
$request->isAjax() === true; // Or wantsJson() === true
throw new \RuntimeException('Database connection failed');
```

---

## Expected Output: JSON response automatically, even without Accept header for AJAX
```json

{
    "error": {
        "code": 500,
        "message": "Database connection failed",
        "type": "RuntimeException"
    }
}
```

---

## Use Case 6: Custom HTTP Exception

# Scenario: Resource not found
```php

// Assuming HttpException exists with getStatusCode() method
// In a route resolver or controller
throw new HttpException('Page not found', 404);
```
---

## Expected Output (Debug mode OFF):

    JSON: {"error": {"code": 404, "message": "An error occurred", "type": "HttpException"}}

    HTML: "404 Not Found" production page with "Return to Homepage" button

## Use Case 7: Fatal Error in Production

# Scenario: Database crashes in production (debug mode OFF)
```php

// Something goes terribly wrong
throw new \PDOException('SQLSTATE[HY000] [2002] Connection refused');
```

---

## Expected Output:

    Log file: /storage/logs/error.log gets entry with timestamp and stack trace
    User sees: "500 Internal Server Error" production page
    JSON clients get generic error message

## Use Case 8: Development Debugging

# Scenario: Developer working locally (debug mode ON)
```php

// Developer makes a mistake
$undefined->method(); // Throws Error
```

---

## Expected Output:

    Detailed HTML page with:
        Error message: "Call to a member function method() on null"
        File and line number
        Full stack trace with "Copy Trace" button
        Gradient background for better readability

## Use Case 9: Log File Management

# Scenario: Monitoring application errors
```php

// Every exception triggers logging
$handler->report($exception);
```

---

## Log File Content (/storage/logs/error.log):
```text

[2024-01-15 14:30:25] PDOException: SQLSTATE[HY000] [2002] Connection refused in /app/Database.php:45
Stack Trace:
#0 /app/Controller.php:30
#1 /app/Kernel.php:25

...

[2024-01-15 14:31:10] InvalidArgumentException: Invalid user ID in /app/UserService.php:89
Stack Trace:
#0 /app/Controller.php:67
...
```
## Use Case 10: Different Response Formats Based on Accept Header

# Scenario: API client vs browser client
```php

// Browser request: Accept: text/html,application/xhtml+xml
// => HTML error page

// API request: Accept: application/json
// => JSON error response

// No Accept header but AJAX request
// => JSON error response
```

---

## Use Case 11: Exception with Custom HTTP Code

# Scenario: Using exception code for HTTP status
```php

// Custom exception with code
$exception = new \Exception('Service unavailable', 503);
// Handler will use 503 as status code
```

---

## Use Case 12: Nested Exception Handling

# Scenario: Previous exception preserved
```php

try {
    // Some operation that fails
} catch (\Exception $previous) {
    throw new AuthException('Authentication failed', 401, $previous);
}
// Log includes both exceptions
```
---

## Real-World Implementation Example:
```php

// In a UserController.php
class UserController
{
    public function showProfile(Request $request, int $userId)
    {
        // Use Case 2: Check authentication
        if (!$this->auth->check()) {
            throw new AuthException('Please login to view profile');
        }
        
        // Use Case 4: Validate input
        if ($userId <= 0) {
            throw new \InvalidArgumentException('Invalid user ID');
        }
        
        // Use Case 6: Check resource exists
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new HttpException('User not found', 404);
        }
        
        // Return successful response
        return new Response(json_encode($user), 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        // Use Case 3: Validation
        $validator = $this->validator->make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|min:2'
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException(
                'Profile update validation failed',
                $validator->errors()->toArray()
            );
        }
        
        // Use Case 5: Database error (AJAX request)
        try {
            $this->userRepository->update($request->all());
        } catch (\PDOException $e) {
            // Use Case 7: Production error
            throw new \RuntimeException('Failed to update profile', 0, $e);
        }
        
        return new Response('Profile updated successfully', 200);
    }
}
```

---


## 🧩 Folder-by-Folder Breakdown

### **1. `app/`**
Houses the main **application logic** — controllers, models, middleware, and exception handlers.

| Subfolder | Purpose |
|------------|----------|
| `Exceptions/` | Centralized exception management (`ExceptionHandler`, `AuthException`) |
| `Http/` | Core HTTP kernel, controllers, middleware |
| `Models/` | Eloquent-like ORM models |
| `Providers/` | Service providers for dependency injection and bootstrapping |

---

### **2. `bootstrap/`**
Contains early-stage initialization scripts that prepare the environment before the kernel runs.

| File | Purpose |
|------|----------|
| `app.php` | Loads `.env`, defines constants, autoloads dependencies |
| `performance.php` | Logs execution time, useful for debugging and performance tracking |

---

### **3. `config/`**
Stores all configuration files, allowing environment-specific overrides.

| File | Purpose |
|------|----------|
| `app.php` | Core application settings (timezone, locale, debug mode) |
| `database.php` | Database connection credentials and drivers |
| `logging.php` | Logging channels and handlers |

---

### **4. `public/`**
The **only web-accessible directory**.  
Contains the front controller (`index.php`) and web assets.

| File | Purpose |
|------|----------|
| `index.php` | Main entry point that bootstraps the framework and dispatches requests |
| `.htaccess` | Redirects all traffic through `index.php` (for Apache) |

---

### **5. `resources/`**
Contains **user-facing resources** like views, language files, and static assets.

| Subfolder | Description |
|------------|--------------|
| `views/` | Templates rendered by the Caremi engine |
| `lang/` | Multi-language translations |
| `assets/` | CSS, JS, and images |

---

### **6. `routes/`**
Holds route definitions that map URLs to controllers or closures.

| File | Purpose |
|------|----------|
| `web.php` | Handles browser-based routes (e.g. `/`, `/contact`) |
| `api.php` | Handles RESTful API routes (`/api/users`) |

---

### **7. `storage/`**
Contains **runtime-generated files** — logs, sessions, cache, and compiled views.

| Folder | Purpose |
|---------|----------|
| `logs/` | Application and error logs |
| `sessions/` | File-based session storage |
| `cache/` | Cached configuration or query results |
| `views/` | Compiled view templates for faster rendering |

---

### **8. `vendor/`**
Contains all **Composer-managed dependencies** and the `autoload.php` file.

---

### **9. Root Files**
| File | Description |
|------|--------------|
| `.env` | Environment configuration |
| `composer.json` | Dependency and autoload management |
| `caremi` | Command-line interface (CLI) entry script |
| `README.md` | Project documentation |

---

### **10. Handle Exceptions**
```php
try {
    // app execution
} catch (AuthException $e) {
    (new ExceptionHandler())->render($e, $request)->send();
} catch (\Throwable $e) {
    (new ExceptionHandler())->render($e, $request)->send();
}
```

## **11. throw AuthException**
throw new AuthException("Access denied.");

### **12. Careminate\Exceptions\Handler.php — Global Exception Handler**
Features

Centralized handling for all application exceptions

Detects debug mode (APP_DEBUG)

Renders HTML or JSON based on Accept headers

Handles both AuthException and generic errors

Example (Debug Mode ON)
```php 
{
  "error": "RuntimeException",
  "message": "Undefined variable",
  "file": "/path/to/file.php",
  "line": 42,
  "trace": [],
  "status": 500
}
```
## **13.Example (Debug Mode OFF)**
```php 
{
  "error": "Server Error",
  "message": "Something went wrong. Please try again later.",
  "status": 500
}
```

## 🧮 Example Flow Diagram

Below is a simplified flow showing how a request moves through Careminate:

```
Request
  │
  ▼
public/index.php
  │
  ▼
bootstrap/app.php
  │
  ▼
app/Http/Kernel.php
  │
  ▼
routes/web.php
  │
  ▼
Controller Action
  │
  ▼
Response Rendered
  │
  ▼
User's Browser
```

---

## 🧰 CLI Integration — `caremi` Tool

The Careminate CLI tool allows developers to **manage and scaffold** parts of the application efficiently.

### Example Commands
```bash
php caremi make:controller HomeController --resource
php caremi make:model User
php caremi migrate
php caremi app:down
php caremi app:up
```

### Command Categories
| Category | Examples |
|-----------|-----------|
| `make:` | Scaffold files (controllers, models, commands) |
| `app:` | Maintenance commands |
| `view:` | Clear compiled views |
| `cache:` | Manage caches |
| `help` | View command list |

---

## 🔧 Development Workflow Summary

| Step | Command | Description |
|------|----------|--------------|
| 1️⃣ | `composer install` | Installs dependencies |
| 2️⃣ | `cp .env.example .env` | Copies and customizes environment config |
| 3️⃣ | `php -S localhost:8000 -t public` | Starts the local dev server |
| 4️⃣ | `php caremi help` | Lists all available commands |
| 5️⃣ | `php caremi make:controller TestController` | Creates a new controller |

---

## 🌍 Multi-Language Example (`resources/lang/`)

### `resources/lang/en/messages.php`
```php
return [
    'welcome' => 'Welcome to Careminate!',
    'error_401' => 'Unauthorized Access',
];
```

### `resources/lang/ar/messages.php`
```php
return [
    'welcome' => 'مرحبًا بك في كيرمينايت!',
    'error_401' => 'دخول غير مصرح به',
];
```

Usage inside controllers or views:
```php
echo trans('messages.welcome');
```

---

## 🧭 Future Additions Roadmap

| Feature | Description | Status |
|----------|--------------|---------|
| 🧩 Service Providers | Modular loading of services | ✅ Implemented |
| 🔐 Validation System | Custom rules, error bags | ✅ Implemented |
| 🗄️ Database ORM | Fluent query builder with PDO | ✅ Implemented |
| ⚙️ Migrations | Database schema management | ✅ Implemented |
| 🧰 CLI Enhancements | Command discovery and stubs | ✅ Implemented |
| 🧱 Routing System | Resourceful and API routes | ✅ Implemented |
| 🧭 Middleware | Request filtering | 🔜 Planned |
| 📡 Event System | Global event dispatching | 🔜 Planned |
| 🧑‍💻 View Engine | Caremi template syntax | ✅ Implemented |

---

## 🏁 Summary

Careminate’s foundation now includes:

- ✅ Environment management (`.env`)
- ✅ Global exception handling
- ✅ Request/Response lifecycle
- ✅ Modular directory structure
- ✅ CLI and service provider system
- ✅ Expandable bootstrap and kernel
- ✅ Ready-to-extend architecture

---

Test Cases to Verify:

    Test authentication failure: curl -I http://localhost/
    Test validation error: curl -X POST http://localhost/profile -H "Accept: application/json"
    Test 404 error: curl http://localhost/nonexistent
    Test debug mode: Set APP_DEBUG=true vs APP_DEBUG=false
    Test log file: Check /storage/logs/error.log after errors
    Test different accept headers: Accept: text/html vs Accept: application/json

The ExceptionHandler provides a comprehensive solution for:

    ✅ Proper HTTP status codes
    ✅ JSON and HTML response formats
    ✅ Debug vs production modes
    ✅ Logging to file
    ✅ User-friendly error pages
    ✅ Detailed developer information
    ✅ Custom exception types
    ✅ Backward compatibility
	
## 🧾 Conclusion

This structure ensures a scalable, organized, and extensible PHP framework foundation.  
It provides a clear path for future modules such as middleware, routing, ORM, and templating.


