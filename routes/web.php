<?php declare(strict_types=1);

use App\Http\Controllers\HomeController;
use Careminate\Supports\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->setName('home');

Route::get('/users/{id}', [HomeController::class, 'show'])->setName('users.show');

Route::get('/about', function () {
    return 'About page';
});

Route::get('/ping', function () {
    return ['pong' => true];
})->setName('ping');