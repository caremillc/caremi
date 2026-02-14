<?php // routes/web.php

return [
        ['GET', '/', [App\Http\Controllers\HomeController::class, 'index']],
        
        ['GET', '/posts', [App\Http\Controllers\Post\PostController::class, 'index']],
        ['GET', '/posts/create', [App\Http\Controllers\Post\PostController::class, 'create']],
        ['POST', '/posts/store', [App\Http\Controllers\Post\PostController::class, 'store']],
        ['GET', '/posts/{id:\d+}/show', [\App\Http\Controllers\Post\PostController::class, 'show']],
        ['GET', '/posts/{id:\d+}/edit', [App\Http\Controllers\Post\PostController::class, 'edit']],
        ['PUT', '/posts/{id:\d+}/update', [App\Http\Controllers\Post\PostController::class, 'update']],
        ['DELETE', '/posts/{id:\d+}/delete', [App\Http\Controllers\Post\PostController::class, 'delete']],
];