<?php

use Careminate\Routing\Route;
use Careminate\Http\Requests\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Invokable\InvokableController;

// Test basic route
// closure
Route::get('/closure', function (Request $request) {
    return new \Careminate\Http\Responses\Response("Closure executed with query");
});

// invoke
Route::get('/invoke', InvokableController::class);

// home
Route::get('/', [HomeController::class, 'index']); 

// Test parameterized route
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}/show', [PostController::class, 'show']);

Route::get('/admin/dashboard', [DashboardController::class, 'index']);