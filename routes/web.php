<?php 

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvokeController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\User\UserController;


// Closure
Route::get('/status', function(){
    echo 'ok';
});

// RESTful fallback
Route::get('/invoke', InvokeController::class); // __invoke()

Route::get('/', HomeController::class, 'index');
Route::get('/about', HomeController::class, 'about');

// posts
Route::get('/posts', PostController::class, 'index')->name('posts.index');
Route::get('/posts/create', PostController::class, 'create')->name('posts.create');
Route::post('/posts/store', PostController::class, 'store')->name('posts.store');
Route::get('/posts/{id}/edit', PostController::class, 'edit')->name('posts.edit');
Route::put('/posts/{id}/update', PostController::class, 'update')->name('posts.update');
Route::get('/posts/{id}/show', PostController::class, 'show')->name('posts.show');
Route::delete('/posts/{id}/delete', PostController::class, 'destroy')->name('posts.destroy');

// routes/web.php
Route::get('/users/{id}/profile', UserController::class, 'show')->defaults(['id' => '1'])->name('user.profile');

// in template:
route('user.profile'); 
// → /users/1/profile

route('user.profile', ['id' => 7]); 
// → /users/7/profile

route('user.profile', ['id' => 7, 'tab' => 'settings']); 
// → /users/7/profile?tab=settings

Route::getRouteNameByUri('/posts/12/show');       // returns "posts.show"
Route::getRouteNameByAction(PostController::class, 'show'); // "posts.show"

Route::get('/users/{id}/profile', UserController::class, 'profile');
Route::where('id', '\d+'); // numeric id only

Route::get('/posts/{slug}', PostController::class, 'show');
Route::where('slug', '[a-z0-9-]+'); // enforce slug format