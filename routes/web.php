<?php // routes/web.php

use App\Http\Middlewares\DummyMiddleware;
use Careminate\Http\Middlewares\Authenticate;
use Careminate\Http\Responses\Response;


return [
   //home
   ['GET', '/', [App\Http\Controllers\HomeController::class, 'index']],

//posts
   ['GET', '/posts', [App\Http\Controllers\Post\PostController::class, 'index']],
   ['GET', '/posts/create', [App\Http\Controllers\Post\PostController::class, 'create']],
   ['POST', '/posts/store', [App\Http\Controllers\Post\PostController::class, 'store']],
   ['GET', '/posts/{id}/show', [\App\Http\Controllers\Post\PostController::class, 'show']],
   ['GET', '/posts/{id}/edit', [App\Http\Controllers\Post\PostController::class, 'edit']],
   ['PUT', '/posts/{id}/update', [App\Http\Controllers\Post\PostController::class, 'update']],
   ['DELETE', '/posts/{id}/delete', [App\Http\Controllers\Post\PostController::class, 'delete']],

   //users
   ['GET', '/users', [App\Http\Controllers\User\UserController::class, 'index']],
   ['GET', '/users/create', [App\Http\Controllers\User\UserController::class, 'create']],
   ['POST', '/users/store', [App\Http\Controllers\User\UserController::class, 'store']],
   ['GET', '/users/{id}/show', [\App\Http\Controllers\User\UserController::class, 'show']],
   ['GET', '/users/{id}/edit', [App\Http\Controllers\User\UserController::class, 'edit']],
   ['PUT', '/users/{id}/update', [App\Http\Controllers\User\UserController::class, 'update']],
   ['DELETE', '/users/{id}/delete', [App\Http\Controllers\User\UserController::class, 'delete']],

    //register 
    ['GET', '/register', [\App\Http\Controllers\Auth\RegistrationController::class, 'index']],
    ['POST', '/register', [\App\Http\Controllers\Auth\RegistrationController::class, 'register']],
 
       //login
     ['GET', '/login', [\App\Http\Controllers\Auth\LoginController::class, 'loginForm']],
     ['POST', '/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']],
    
     //dashboard 
    ['GET', '/admin/dashboard', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index',[Authenticate::class, DummyMiddleware::class]]],

//response
   ['GET', '/Hello/{name:.+}', function (string $name) {
       return new Response("Hello $name");
   }],

   
];