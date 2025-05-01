<?php 

use Careminate\Routing\Route;
use Careminate\Http\Responses\Response;
use App\Http\Controllers\Api\ApiController;


Route::get('/api', function() {
    return Response::json('Test route works')->send();
    
});

Route::get('/api/posts', [ApiController::class, 'getPosts']);