<?php 

use Careminate\Routing\Route;
use App\Http\Controllers\HomeController;

Route::get('/', HomeController::class,'index');
Route::get('/about', HomeController::class,'about');
