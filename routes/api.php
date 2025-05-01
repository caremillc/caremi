<?php 

use Careminate\Routing\Route;
use App\Http\Middlewares\Authenticate;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PostController;

Route::get('/api', function() {
    return response()->json([
        'message' => 'API is working',
        'version' => '1.0'
    ]);
})->name('api.index');

// Individual API Post Routes
Route::get('/api/posts', [ApiController::class, 'index'])->name('api.posts.index');
Route::post('/api/posts/store', [ApiController::class, 'store'])->name('api.posts.store');
Route::get('/api/posts/{id}/show', [ApiController::class, 'show'])->name('api.posts.show');
Route::put('/api/posts/{id}/edit', [ApiController::class, 'edit'])->name('api.posts.edit');
Route::patch('/api/posts/{id}/update', [ApiController::class, 'update'])->name('api.posts.update');
Route::delete('/api/posts/{id}/delete', [ApiController::class, 'destroy'])->name('api.posts.destroy');

// RESTful API Resource Route (alternative to the above individual routes)
Route::apiResource('/api/posts', ApiController::class);

Route::group(['prefix' => 'api', 'middleware' => [Authenticate::class]], function() {
    // API resource routes with authentication
    Route::apiResource('/users', ApiController::class);
    Route::apiResource('/posts', ApiController::class);
    
    // Nested resource example
    Route::apiResource('/posts.comments', PostCommentController::class);
});

// Another API resource example
Route::apiResource('/api/posts', ApiController::class);

// API resource with excluded methods
Route::apiResource('posts', ApiController::class)->except(['update']);

// API resource with only specific methods
Route::apiResource('posts', ApiController::class)->only(['index', 'show']);

// API resource with middleware
Route::apiResource('posts', ApiController::class)->middleware(['auth.api']);
