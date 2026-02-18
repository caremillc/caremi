<?php // routes/web.php

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

//response
   ['GET', '/Hello/{name:.+}', function (string $name) {
       return new Response("Hello $name");
   }],

];