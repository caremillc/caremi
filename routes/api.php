<?php 

use Careminate\Routing\Route;
use App\Http\Middlewares\Authenticate;
use App\Http\Controllers\Api\ApiController;



Route::get('/api', function() {
    return response()->json([
        'message' => 'API is working',
        'version' => '1.0'
    ]);
})->name('api.index');

// Posts Resource Routes
Route::get('/api/posts', [ApiController::class, 'index'])->name('api.posts.index');
Route::post('/api/posts/store', [ApiController::class, 'store'])->name('api.posts.store');
Route::get('/api/posts/{id}/show', [ApiController::class, 'show'])->name('api.posts.show');
Route::put('/api/posts/{id}/edit', [ApiController::class, 'edit'])->name('api.posts.edit');
Route::patch('/api/posts/{id}/update', [ApiController::class, 'update'])->name('api.posts.update');
Route::delete('/api/posts/{id}/delete', [ApiController::class, 'destroy'])->name('api.posts.destroy');

Route::group(['prefix' => 'api','middleware' => [Authenticate::class]], function() {
    Route::get('/users', [ApiController::class, 'index']);
    Route::post('/users', [ApiController::class, 'store']);
});