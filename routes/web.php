<?php

use Careminate\Routing\Route;
use App\Http\Middlewares\Middleware;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;

// Correct: closure as controller, null action, middleware in 4th param
// Route::get('/', function () {
//     return "Welcome to the web anonymous page";
// }, null, [Middleware::class . ',admin,user']);

// Controller routes with middleware
Route::group(['prefix'=>'front'], function(){
    Route::get('/', HomeController::class, 'index',[Middleware::class . ',admin,user']);

// Controller routes without middleware
    Route::get('/about', HomeController::class, 'about');
    Route::get('/articles/{id}/{slug}', HomeController::class, 'articles');
});

Route::group(['prefix'=>'admin'], function(){
    // PostController resource-style routes
    Route::get('/posts', PostController::class, 'index');
    Route::get('posts/create', PostController::class, 'create');
    Route::post('posts/store', PostController::class, 'store');
    Route::get('posts/{id}/show', PostController::class, 'show');
    Route::get('posts/{id}/edit', PostController::class, 'edit');
    Route::put('posts/{id}/update', PostController::class, 'update');
    Route::delete('posts/{id}/destroy', PostController::class, 'destroy');
});


