<?php

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvokeController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\User\UserController;
// use App\Http\Controllers\Post\PostController;

// Closure route
Route::get('/', function () {
    return 'Closure route is working!';
});

Route::get('/test-query', function ($request) {
    return 'Query string: ' . $request->query('q');
});

Route::get('/test-query-json', function ($request) {
    return 'Query: ' . json_encode($request->query());
});

// __invoke()
Route::get('/invoke', InvokeController::class); 

// Controller routes
Route::get('/home', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
// posts
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
Route::get('/users/{id}/show', [UserController::class, 'show'])->name('users.show');
Route::delete('/users/{id}/delete', [UserController::class, 'destroy'])->name('posts.destroy');

// posts resource
 Route::resource('posts', [PostController::class]);

// route with  default value
//Route::get('/posts/{id}/show', [PostController::class, 'show'])->defaults(['id' => 1]);
Route::get('/posts/{id?}/show', [PostController::class, 'show'])->defaults(['id' => 1])->name('posts.show');

Route::group(['prefix' => 'admin',  'middleware' => ['auth']], function () {
    Route::get('/posts', [PostController::class,'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
     Route::get('/posts/{id}/show', [PostController::class, 'show'])->name('posts.show');
});
