<?php 

use Careminate\Routing\Route;
use App\Http\Controllers\Post\PostController;

Route::middleware(['api'])->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
});