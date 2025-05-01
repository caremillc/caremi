<?php

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Post\PostController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{id}/show', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{id}/update', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{id}/delete', [PostController::class, 'destroy'])->name('posts.destroy');