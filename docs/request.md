# Careminate Framework - HTTP Requests Documentation

## Overview
The Careminate Framework provides a comprehensive HTTP request handling system through two core classes: `Request` and `UploadedFile`. These classes encapsulate HTTP request data and file uploads with type safety and convenient access methods.

## Request Class (`Careminate\Http\Requests\Request`)

### Creating a Request Instance

#### From PHP Superglobals (Recommended)
```php
use Careminate\Http\Requests\Request;

// Create request from current HTTP context
$request = Request::createFromGlobals();
```

## Manual Construction 
```php
$request = new Request(
    $_GET,
    $_POST,
    $_SERVER,
    $_COOKIE,
    $_FILES
);
```

## Core Properties

All properties are readonly for immutability:

    $get - Query string parameters

    $post - POST data

    $server - Server and environment data

    $cookies - HTTP cookies

    $files - Uploaded files (as UploadedFile objects)

## Essential Methods
1. Request Information
```php 
// Get HTTP method (GET, POST, PUT, DELETE, etc.)
$method = $request->getMethod();

// Get current URI path (without query string)
$path = $request->getUri();

// Get full URL including protocol and query string
$fullUrl = $request->fullUrl();

// Check if request is over HTTPS
$isSecure = $request->isSecure();

// Get client IP address
$ip = $request->getIp();

// Get user agent string
$userAgent = $request->getUserAgent();
```

2. Parameter Access
```php 
// Get query string parameter (GET data)
$page = $request->query('page', 1); // with default value

// Get input parameter (checks POST, GET, route params)
$id = $request->input('id');

// Alias for input()
$userId = $request->getParam('user_id');

// Get all parameters merged (route + GET + POST)
$allData = $request->all();

// Get route parameter (when using routing)
$userId = $request->route('id'); // For route pattern: /users/{id}
```

3. Request Body & Content
```php 
// Get raw request body
$rawContent = $request->getContent();

// Parse JSON request body
$jsonData = $request->json(); // Returns array by default
$jsonObject = $request->json(false); // Returns object

// Check if request expects JSON response
if ($request->wantsJson()) {
    return json_response($data);
}

// Check for AJAX request
if ($request->isAjax()) {
    // Return partial data
}

```
4. Headers
```php

// Get specific header
$authHeader = $request->getHeader('Authorization');
$acceptHeader = $request->getHeader('Accept', 'text/html');
```

## Use Cases
Basic Form Handling
```php 
// In your controller
public function handleForm(Request $request)
{
    // Get form data
    $name = $request->input('name');
    $email = $request->input('email');
    
    // Validate required fields
    if (empty($name) || empty($email)) {
        return redirect()->back()->withErrors(['All fields are required']);
    }
    
    // Process data...
}
```

# API Endpoint
```php 
public function apiEndpoint(Request $request)
{
    // Only respond with JSON for API requests
    if (!$request->wantsJson() && !$request->isAjax()) {
        return response('Not acceptable', 406);
    }
    
    // Parse JSON body
    $data = $request->json();
    
    if (!$data) {
        return json_response(['error' => 'Invalid JSON'], 400);
    }
    
    // Process and return JSON
    return json_response(['success' => true, 'data' => $processedData]);
}
```

# Route Parameter Usage
```php 
// Route definition: GET /products/{id}
$router->get('/products/{id}', function (Request $request) {
    $productId = $request->route('id');
    $queryParam = $request->query('details', 'basic');
    
    return view('product', [
        'id' => $productId,
        'details' => $queryParam
    ]);
});
```
# UploadedFile Class (Careminate\Http\UploadedFile)
Structure

The UploadedFile class represents a single uploaded file with these readonly properties:

    $originalName - Original filename from client

    $tmpPath - Temporary storage path

    $mimeType - File MIME type

    $size - File size in bytes

    $error - Upload error code

## Methods
# Basic Usage
```php

public function handleUpload(Request $request)
{
    // Assuming file input name is "document"
    $file = $request->files['document'] ?? null;
    
    if ($file instanceof UploadedFile) {
        // Validate upload
        if ($file->isValid()) {
            // Move to permanent location
            $destination = '/uploads/' . uniqid() . '_' . $file->originalName;
            
            if ($file->moveTo($destination)) {
                return "File uploaded successfully to: $destination";
            }
        }
    }
    
    return "Upload failed";
}
```

# File Validation Example
```php

public function validateAndSave(UploadedFile $file)
{
    // Check upload success
    if (!$file->isValid()) {
        throw new Exception('File upload failed with error code: ' . $file->error);
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file->mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type: ' . $file->mimeType);
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file->size > $maxSize) {
        throw new Exception('File too large. Max size: 5MB');
    }
    
    // Sanitize filename
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '', $file->originalName);
    $destination = '/uploads/' . date('Y-m-d') . '/' . $safeName;
    
    // Ensure directory exists
    $dir = dirname($destination);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Save file
    if ($file->moveTo($destination)) {
        return $destination;
    }
    
    throw new Exception('Failed to save file');
}
```

## Working with Multiple Files
```php

public function handleMultipleUploads(Request $request)
{
    $results = [];
    
    foreach ($request->files as $field => $file) {
        // Handle single UploadedFile
        if ($file instanceof UploadedFile) {
            if ($file->isValid()) {
                $results[$field] = $this->saveFile($file);
            }
        }
        // Handle array of files (multiple="multiple" attribute)
        elseif (is_array($file)) {
            foreach ($file as $uploadedFile) {
                if ($uploadedFile->isValid()) {
                    $results[$field][] = $this->saveFile($uploadedFile);
                }
            }
        }
    }
    
    return $results;
}
```

## Best Practices & Security Considerations
1. Always Validate Input
```php

// Use type hints and validation
public function updateUser(Request $request, int $userId)
{
    $email = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new InvalidArgumentException('Invalid email address');
    }
}
```

2. File Upload Security
```php

// Never trust original filename
$safeName = sprintf(
    '%s_%s%s',
    date('Ymd_His'),
    bin2hex(random_bytes(8)),
    $this->getExtensionByMimeType($file->mimeType)
);


// Restrict file types by MIME, not extension
private function getExtensionByMimeType(string $mimeType): string
{
    $map = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'application/pdf' => '.pdf',
    ];
    
    return $map[$mimeType] ?? '.bin';
}
```
3. CSRF Protection
```php

// Always verify CSRF tokens for state-changing requests
if ($request->getMethod() === 'POST') {
    $token = $request->input('_token');
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        throw new Exception('CSRF token validation failed');
    }
}
```
4. Rate Limiting
```php

// Implement basic rate limiting
public function apiHandler(Request $request)
{
    $ip = $request->getIp();
    $key = 'rate_limit:' . $ip;
    
    $attempts = Cache::get($key, 0);
    if ($attempts > 100) {
        return response('Too many requests', 429);
    }
    
    Cache::increment($key);
}
```
## Integration Examples
# Middleware Example
```php

class AuthenticationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->getHeader('Authorization');
        
        if (!$this->validateToken($token)) {
            return $request->wantsJson()
                ? json_response(['error' => 'Unauthorized'], 401)
                : redirect('/login');
        }
        
        return $next($request);
    }
}
```
## Validation Service
```php

class FormValidator
{
    public function validate(Request $request, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $request->input($field);
            
            if ($rule === 'required' && empty($value)) {
                $errors[$field] = "$field is required";
            }
            
            if (strpos($rule, 'max:') === 0) {
                $max = (int) substr($rule, 4);
                if (strlen($value) > $max) {
                    $errors[$field] = "$field must be at most $max characters";
                }
            }
        }
        
        return $errors;
    }
}
```
## Common Patterns
1. Request Macros (Extension Pattern)
```php

// Extend Request functionality
trait RequestMacros
{
    public function isAdminRequest(): bool
    {
        return strpos($this->getUri(), '/admin/') === 0;
    }
    
    public function preferredFormat(): string
    {
        if ($this->wantsJson()) return 'json';
        if (str_contains($this->getHeader('Accept'), 'xml')) return 'xml';
        return 'html';
    }
}
```

2. API Response Helper
```php

function api_response(Request $request, $data, $status = 200)
{
    $format = $request->preferredFormat();
    
    switch ($format) {
        case 'json':
            return json_response($data, $status);
        case 'xml':
            return xml_response($data, $status);
        default:
            return response($data, $status);
    }
}
```

## Migration & Compatibility
# From Native PHP to Careminate Request
```php

// Old way
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// New way
$request = Request::createFromGlobals();
$id = $request->input('id');
$method = $request->getMethod();
```
## Handling Legacy Code
```php

// If you need to access superglobals temporarily
global $request; // Your Request instance

// Or create adapter
function legacy_get($key, $default = null)
{
    global $request;
    return $request->query($key, $default);
}
```
## Testing
# Mocking Requests in Tests
```php

class UserControllerTest extends TestCase
{
    public function testCreateUser()
    {
        $request = new Request(
            [], // GET
            ['name' => 'John', 'email' => 'john@example.com'], // POST
            ['REQUEST_METHOD' => 'POST'], // SERVER
            [], // COOKIES
            [] // FILES
        );
        
        $controller = new UserController();
        $response = $controller->store($request);
        
        $this->assertEquals(201, $response->getStatusCode());
    }
    
    public function testFileUpload()
    {
        $file = new UploadedFile(
            'document.pdf',
            '/tmp/php1234.tmp',
            'application/pdf',
            1024,
            UPLOAD_ERR_OK
        );
        
        $request = new Request([], [], [], [], ['document' => $file]);
        
        $this->assertTrue($file->isValid());
    }
}
```

## Troubleshooting
# Common Issues
1. Missing route parameters
```php

// Ensure router sets parameters
$router->get('/users/{id}', function (Request $request) {
    // This requires the router to call $request->setRouteParams()
    $id = $request->route('id');
});
```
2. JSON parsing returns null
```php

// Check content type header
if ($request->getHeader('Content-Type') !== 'application/json') {
    // Client must send correct header
}

// Or force parse
$data = json_decode($request->getContent(), true);
```
3. File upload validation
```php

// Always check error code
if ($file->error !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
        // ... other error codes
    ];
    throw new Exception($errors[$file->error] ?? 'Upload failed');
}
```
## Performance Considerations

    The Request object is immutable after construction (readonly properties)

    Use createFromGlobals() for production, manual construction for testing

    JSON parsing is lazy (only when json() is called)

    Large file uploads should be streamed or processed in chunks

## Version Notes

This documentation covers version 1.0 of the Careminate HTTP Request system. Key features:

    Strict typing throughout

    Immutable request data

    Comprehensive file upload handling

    JSON request support

    Route parameter integration

    Full testability

For updates and changes, refer to the framework changelog.
## Quick Reference Cheat Sheet
# Request Methods Quick Reference
```php

// Creation
$request = Request::createFromGlobals();

// HTTP Info
$request->getMethod();           // GET, POST, etc.
$request->getUri();              // /path/without/query
$request->fullUrl();             // Full URL
$request->isSecure();            // HTTPS check

// Data Access
$request->query('key');          // GET parameter
$request->input('key');          // POST, GET, or route
$request->route('key');          // Route parameter
$request->all();                 // All merged data

// Headers & Content
$request->getHeader('Accept');
$request->getContent();          // Raw body
$request->json();                // JSON parsed
$request->wantsJson();           // Check Accept header
$request->isAjax();              // X-Requested-With check

// Client Info
$request->getIp();
$request->getUserAgent();
```
## UploadedFile Quick Reference
```php

// Validation
$file->isValid();                // Check upload success

// Properties
$file->originalName;             // Original filename
$file->mimeType;                 // File MIME type
$file->size;                     // Size in bytes
$file->error;                    // Upload error code

// Operations
$file->moveTo('/path/file.ext'); // Save to destination
```
## License & Support

This documentation is part of the Careminate Framework. For support, issues, or contributions, please refer to the official framework repository and documentation.

Documentation generated for Careminate Framework v1.0.0
Last updated: December 2024

This markdown file contains the complete documentation for the Careminate Framework's HTTP Request system. You can download and use this as a reference guide for development with the framework.
text
 
