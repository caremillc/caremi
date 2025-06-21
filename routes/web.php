<?php

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;

// Closure route
Route::get('/', function () {
    return 'anonymous route is working!';
});

// Controller routes
Route::get('/home', HomeController::class, 'index');
Route::get('/about', HomeController::class, 'about');
Route::get('/article/{id}/{slug}', HomeController::class, 'article');
