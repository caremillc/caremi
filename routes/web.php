<?php

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvokeController;
use App\Http\Controllers\Post\PostController;

// Closure route
Route::get('/', function () {
    return 'Anonymous route is working!';
});

Route::get('/test', function ($request) {
    return 'Query string: ' . $request->query('q');
});

Route::get('/test-query', function ($request) {
    return 'Query: ' . json_encode($request->query());
});
// __invoke()
Route::get('/invoke', InvokeController::class); 

// Controller routes
Route::get('/home', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
// posts
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{id}/update', [PostController::class, 'update'])->name('posts.update');
// Route::get('/posts/{id}/show', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{id}/show', [PostController::class, 'show'])->defaults(['id' => 1]); // route with  default value
Route::delete('/posts/{id}/delete', [PostController::class, 'destroy'])->name('posts.destroy');

// posts resource
Route::resource('posts', [PostController::class]);