<?php 

use Careminate\Routing\Route;
use Careminate\FrameworkSetting;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Locale\LocaleController;

Route::get('/', HomeController::class, 'index');
// Route::get('/', function() {
//     FrameworkSetting::setLocale('ar');
//     return trans('app.info');
// });

Route::group(['prefix'=>'front'], function(){
    Route::get('/about', HomeController::class,'about');
});

Route::group(['prefix'=>'admin'], function(){
    Route::get('/posts', PostController::class, 'index');
    Route::get('posts/create', PostController::class, 'create');
    Route::post('posts/store', PostController::class, 'store');
    Route::get('posts/{id}/show', PostController::class, 'show');
    Route::get('posts/{id}/edit', PostController::class, 'edit');
    Route::put('posts/{id}/update', PostController::class, 'update');
    Route::delete('posts/{id}/destroy', PostController::class, 'destroy');
});

Route::get('/set-locale/{locale}', [LocaleController::class, 'setLocale']);
Route::get('/show-locale', [LocaleController::class, 'showLocale']);