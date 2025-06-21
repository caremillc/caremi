<?php 

use Careminate\Routing\Route;


Route::group(['prefix' => 'api', 'namespace' => 'App\\Http\\Controllers\\Api'], function () {
    Route::get('/posts', [PostApiController::class,'index'])->name('api.posts.index');
});