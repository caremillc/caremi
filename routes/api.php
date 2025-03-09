<?php

use Careminate\Routing\Route;
use Careminate\FrameworkSetting;
use App\Http\Middlewares\Middleware;
use App\Http\Middlewares\AdminMiddleware;
use App\Http\Controllers\Api\ApiController;

Route::group(['prefix'=>'/api/','middleware'=>[Middleware::class]], function(){  //this
    //api
    Route::get('/', ApiController::class, 'index', [AdminMiddleware::class]);
    
      //api/users
    Route::get('/users', function(){
        return 'Welcome to users api routes';
    },[AdminMiddleware::class]);

     //api/article
     Route::get('/articles', function(){
        return 'Welcome to article api routes';
    });

   
 });
