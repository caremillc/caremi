<?php 

use Careminate\Routing\Route;
use App\Http\Controllers\Api\ApiController;

Route::group(['prefix' => 'api', 'middleware' => [App\Http\Middlewares\Middleware::class]], function () {

    Route::get('/', [ApiController::class, 'index']);
    Route::get('users/{id}', [ApiController::class, 'show']);
   

    // Route with closure
    Route::get('hello', function () {
        return 'Hello, API!';
    });
}); 


