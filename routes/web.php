<?php 

use Careminate\Routing\Route;
use App\Http\Middlewares\Middleware;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;
        
        
Route::get('/', HomeController::class,'index');
// Route::get('/', function(){
//     return 'anonymouse route';
// },[]);
Route::get('/about', HomeController::class,'about');
Route::get('/articles/{id}/{slug}', HomeController::class, 'articles');

Route::get('/posts', PostController::class, 'index');
Route::get('posts/create', PostController::class, 'create');
Route::post('posts/store', PostController::class, 'store');
Route::get('posts/{id}/show', PostController::class, 'show');
Route::get('posts/{id}/edit', PostController::class, 'edit');
Route::put('posts/{id}/update', PostController::class, 'update');
Route::delete('posts/{id}/destroy', PostController::class, 'destroy');
