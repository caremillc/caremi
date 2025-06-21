<?php

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvokeController;
use App\Http\Controllers\Post\PostController;

// Closure route
Route::get('/', function () {
    return 'Test route is working!';
});

// __invoke()
Route::get('/invoke', InvokeController::class); 

// Controller routes
Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
// posts
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/create', [PostController::class, 'create']);
Route::post('/posts/store', [PostController::class, 'store']);
Route::get('/posts/{id}/edit', [PostController::class, 'edit']);
Route::put('/posts/{id}/update', [PostController::class, 'update']);
Route::get('/posts/{id}/show', [PostController::class, 'show']);
Route::delete('/posts/{id}/delete', [PostController::class, 'destroy']);

// posts resource
Route::resource('posts', [PostController::class]);

?>
