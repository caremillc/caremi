<?php declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\AuthMiddleware;
use Careminate\Supports\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->setName('home');

Route::get('/login', function () {
    return 'Login page';
})->setName('login');

Route::group(['prefix' => 'dashboard','as' => 'dashboard.','middleware' => [AuthMiddleware::class],], function () {
    Route::get('/', [DashboardController::class, 'index'])->setName('home');
    Route::get('/settings', [DashboardController::class, 'settings'])->setName('settings');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.',], function () {
    Route::group([ 'prefix' => 'users', 'as' => 'users.', ], function () {
        Route::get('/{id}', [HomeController::class, 'show'])->setName('show');
    });
});

Route::group(['prefix' => 'dashboard','as' => 'dashboard.','middleware' => [AuthMiddleware::class],], function () {
    Route::get('/settings', [DashboardController::class, 'settings'])->setName('settings');
});