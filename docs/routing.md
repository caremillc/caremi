# Router System Documentation

## Overview

The router handles HTTP request routing with support for:
- Route parameters (required and optional)
- Default parameter values
- Route naming and reverse routing
- Controller method binding
- Invokable controllers
- Middleware support

## Basic Routing

### Defining Routes

```php
// Closure route
Route::get('/', function() {
    return 'Welcome!';
});

// Controller method
Route::get('/posts', [PostController::class, 'index']);

// Invokable controller 
Route::get('/status', StatusController::class);
```
# Route Parameters
## Required Parameters
```php

Route::get('/posts/{id}', [PostController::class, 'show']);
```

# Optional Parameters
```php

Route::get('/posts/{id?}', [PostController::class, 'show']);
```

# Default Parameter Values
```php

// Will use id=1 if no id provided
Route::get('/posts/{id}/show', [PostController::class, 'show'])
    ->defaults(['id' => 1]);
```
# Behavior:

    /posts/5/show → $id = 5

    /posts/show → $id = 1 (uses default)

# Named Routes & URL Generation
## Creating Named Routes
```php

Route::get('/posts', [PostController::class, 'index'])
    ->name('posts.index');
```
# Generating URLs
```php

// Basic URL generation
$url = Router::route('posts.index');

// With parameters (overrides defaults)
$url = Router::route('posts.show', ['id' => 5]);

// With query string
$url = Router::route('posts.index', ['page' => 2]);
// Returns: /posts?page=2
```
# Default Parameter Handling in URL Generation:
```php

// Given route with defaults:
Route::get('/posts/{id}/show', [PostController::class, 'show'])
    ->name('posts.show')
    ->defaults(['id' => 1]);

// Will use default if not provided
$url = Router::route('posts.show'); // /posts/1/show
```
# Route Groups
```php

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'users']);
});
```
# Middleware
```php

Route::get('/profile', [UserController::class, 'profile'])
    ->middleware(['auth', 'verified']);
```
# Resourceful Routes
```php

Route::resource('posts', PostController::class);
```
# Creates routes for all CRUD operations with default naming:

Method	| URI	| Action	| Route Name

GET	     |/posts| 	index	| posts.index
GET	/posts/create	create	posts.create
POST	/posts	store	posts.store
GET	/posts/{id}	show	posts.show
GET	/posts/{id}/edit	edit	posts.edit
PUT	/posts/{id}	update	posts.update
DELETE	/posts/{id}	destroy	posts.destroy

# Advanced Route Patterns
## Custom Regex Constraints
```php

Router::pattern('id', '[0-9]+');
```
# Complex Parameter Handling
```php

Route::get('/{category}/{post}/{comment?}', 
    [PostController::class, 'show'])
    ->defaults([
        'category' => 'news',
        'comment' => 'latest'
    ]);
```
# Best Practices

    Use named routes for maintainable URL generation

    Set default values for optional parameters

    Group related routes with common settings

    Validate parameters in controller methods

    Document complex routes with comments

# Error Handling
## Route Not Found

Returns 404 response automatically
Method Not Allowed

Returns 405 if route exists but method doesn't match
Missing Required Parameters

Throws exception with descriptive message
Complete Example
```php

Route::group(['prefix' => 'api/v1', 'middleware' => 'api'], function() {
    
    Route::get('/status', ApiStatusController::class)
        ->name('api.status');
        
    Route::resource('posts', PostController::class)
        ->only(['index', 'show'])
        ->defaults(['page' => 1]);
        
    Route::get('/search/{query}', [SearchController::class, 'query'])
        ->defaults(['query' => ''])
        ->name('api.search');
});
```
text


Key updates from previous version:
1. Added detailed documentation for default parameter functionality
2. Explained behavior with URL generation
3. Added examples showing interaction between defaults and parameters
4. Included best practices for using defaults
5. Enhanced complex example to show defaults in context

The documentation maintains all previous content while adding comprehensive coverage of the new default parameters feature.
