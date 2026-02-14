<?php

use Careminate\Http\Responses\Response;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;

return [

    ['GET', '/', [HomeController::class, 'index']],

    ['GET', '/posts', [PostController::class, 'index']],
    ['GET', '/posts/create', [PostController::class, 'create']],
    ['POST', '/posts/store', [PostController::class, 'store']],

    ['GET', '/posts/{id:\d+}/show', [PostController::class, 'show']],
    ['GET', '/posts/{id:\d+}/edit', [PostController::class, 'edit']],
    ['PUT', '/posts/{id:\d+}/update', [PostController::class, 'update']],
    ['DELETE', '/posts/{id:\d+}/delete', [PostController::class, 'delete']],

    // Closure Route Example
    ['GET', '/hello/{name:.+}', function (string $name) {
        return new Response("Hello $name");
    }],

];
