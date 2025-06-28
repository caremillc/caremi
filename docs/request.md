# Request Handling System

## Overview

The request system provides:
- HTTP request abstraction
- File upload handling
- Input validation
- Convenience methods for common request operations

## Key Components

1. `Request` - Main HTTP request handler
2. `FileRequest` - File upload handling
3. `FormRequest` - Validation-enabled requests
4. `UploadedFile` - Individual file operations

## Basic Usage

### Accessing Request Data

```php
use Careminate\Http\Requests\Request;

// Get all input
$input = Request::capture()->all();

// Get specific value
$name = Request::capture()->get('name');

// Using helper
$email = request('email');
```

# Request Methods
```php 
$request = Request::capture();

if ($request->isMethod('POST')) {
    // Handle POST
}

// Shortcuts
$request->isGet();
$request->isPost(); 
$request->isPut();
$request->isDelete();
```

# File Uploads
```php

use Careminate\Http\Requests\FileRequest;

// Get uploaded file
$file = FileRequest::file('avatar');

// Using helper
$file = file_request('avatar');
```

# File Handling
## UploadedFile Methods
```php

if ($file->isValid()) {
    // Original name
    $name = $file->getClientOriginalName();
    
    // MIME type
    $type = $file->getClientMimeType();
    
    // File size
    $size = $file->getSize();
    
    // Check if image
    if ($file->isImage()) {
        // ...
    }
    
    // Check extension
    if ($file->hasExtension(['jpg', 'png'])) {
        // ...
    }
}
```

# Storing Uploads
```php

// Basic storage
$path = $file->store('uploads/images');

// Custom filename
$path = $file->storeAs('uploads', 'custom-name.jpg');

// Full control
$file->move('/full/path/to/directory', 'filename.ext');
```

# Validation
## FormRequest Usage
```php

namespace App\Http\Requests\User;

use Careminate\Http\Requests\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add auth logic
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'avatar' => 'image|max:2048', // 2MB max
        ];
    }
    
    public function messages(): array
    {
        return [
            'email.unique' => 'Email already registered'
        ];
    }
}
```

# Controller Usage
```php

public function store(StoreUserRequest $request)
{
    // Data is already validated
    $validated = $request->validated();
    
    // Store user...
}
```

# Manual Validation
```php

use Careminate\Http\Validations\Validate;

$data = request()->all();
$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8'
];

$validator = new Validate($data, $rules);

if ($validator->fails()) {
    $errors = $validator->errors();
    // Handle errors
}
```

# Available Validation Rules
Rule	            Description	                  Example
required	   Field must be present	       'name' => 'required'
string	            Must be a string	       'title' => 'string'
email	          Valid email format	       'email' => 'email'
min	             Minimum length/value	       'pass' => 'min:8'
max	             Maximum length/value	       'title' => 'max:100'
confirmed	      Matching confirmation field  'pass' => 'confirmed'
numeric	           Must be numeric	           'age' => 'numeric'
integer	             Must be integer	       'count' => 'integer'
boolean	               Must be boolean	       'active' => 'boolean'
array	             Must be array	           'tags' => 'array'
in	                Value in given set	       'role' => 'in:admin,user'
not_in	          Value not in given set	   'status' => 'not_in:banned'
same	         Match another field	       'token' => 'same:csrf_token'
different	     Differ from another field	   'new_pass' => 'different:old_pass'
date	             Valid date	               'dob' => 'date'
url	                  Valid URL	               'website' => 'url'
regex	           Match regex pattern	       'sku' => 'regex:/^[A-Z]{3}\d+$/'


# File Validation

## Validate uploaded files with these additional rules:
```php

'avatar' => 'required|image|mimes:jpeg,png|max:2048', // 2MB JPEG/PNG
'document' => 'file|mimes:pdf,docx|max:5120' // 5MB PDF/DOCX
```

# Helpers
## Request Helpers
```php

// Get request instance
request();

// Get input value
request('email', 'default@example.com');

// Check if input exists
request()->has('token');

// Get only certain inputs
request()->only(['name', 'email']);

// Get all except some
request()->except(['password']);
```

# File Helpers
```php

// Check if file exists
file_request()->hasFile('avatar');

// Get all files
$files = file_request()->all();

// Move uploaded file
file_request()->move('avatar', 'path/to/destination');
```

# Advanced Features
## Custom Validation Rules

## Extend the Validate class:
```php

class CustomValidator extends Validate
{
    protected function validateCustomRule($field, $value, $params)
    {
        // Custom logic
        return $condition;
    }
}
```
# Request Macros

## Add custom methods to the Request class:
```php

Request::macro('isAdmin', function() {
    return $this->get('role') === 'admin';
});

// Usage
request()->isAdmin();
```

# Error Handling
## Validation Errors
```php

// In FormRequest
protected function failedValidation()
{
    throw new ValidationException($this->errors());
}

// Manual handling
if ($validator->fails()) {
    return back()->withErrors($validator->errors());
}
```

# File Upload Errors
```php

if ($file->getError() !== UPLOAD_ERR_OK) {
    throw new UploadException($file->getError());
}
```

# Best Practices

    Always validate - Never trust user input

    Use FormRequests for complex validation

    Sanitize filenames before storage

    Set upload limits in php.ini

    Validate MIME types don't rely on extensions

    Handle failures gracefully with user feedback

text


This documentation covers:
- Basic to advanced usage
- Validation rules reference
- File handling specifics
- Helper functions
- Error handling
- Best practices

The markdown renders properly on GitHub/GitLab and provides comprehensive guidance for developers using your request handling system.
	
