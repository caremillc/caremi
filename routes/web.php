<?php

use Careminate\Routing\Route;
use App\Http\Middlewares\Authenticate;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Individual Post Routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{id}/show', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{id}/update', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{id}/delete', [PostController::class, 'destroy'])->name('posts.destroy');

// RESTful Resource Route (alternative to the above individual routes)
Route::resource('/posts', PostController::class)->except(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

Route::group(['prefix' => 'admin', 'middleware' => [Authenticate::class]], function() {
    // Admin resource routes
    Route::resource('/users', PostController::class);
    Route::resource('/posts', PostController::class);
});

