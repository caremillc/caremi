<?php

use Careminate\Locale\Lang;
use Careminate\Routing\Route;
use Careminate\FrameworkSetting;
use Careminate\Sessions\Session;
use App\Http\Middlewares\Middleware;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;

Route::get('/', HomeController::class, 'index');

// Route::get('/', function(){
//     FrameworkSetting::setLocale('en');
//     // FrameworkSetting::setLocale('en');
//     // return Lang::get('setting.site');
//     return trans('name');
//  });
// Controller routes with middleware
Route::group(['prefix'=>'front'], function(){
    //home
    Route::get('/', HomeController::class, 'index',[Middleware::class . ',admin,user']);

    // Controller routes without middleware
    Route::get('/about', HomeController::class, 'about');
    Route::get('/articles/{id}/{slug}', HomeController::class, 'articles');
});

Route::group(['prefix'=>'admin'], function(){
    // PostController resource-style routes
    Route::get('/posts', PostController::class, 'index');
    Route::get('posts/create', PostController::class, 'create');
    Route::post('posts/store', PostController::class, 'store');
    Route::get('posts/{id}/show', PostController::class, 'show');
    Route::get('posts/{id}/edit', PostController::class, 'edit');
    Route::put('posts/{id}/update', PostController::class, 'update');
    Route::delete('posts/{id}/destroy', PostController::class, 'destroy');

    //dashboard
    Route::get('dashboard', PostController::class, 'index');

});


