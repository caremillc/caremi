<?php

use App\Http\Controllers\HomeController;
use Careminate\Routing\Route;

Route::add('GET', '/', function () {
    return 'anonymous route is working!';
});

Route::add('GET', '/home', [HomeController::class, 'index']);
Route::add('POST', '/home', [HomeController::class, 'store']);

