<?php

use Careminate\Routing\Route;
use App\Http\Middlewares\Middleware;
use App\Http\Middlewares\AdminMiddleware;


Route::group(['prefix'=>'/api/','middleware'=>[Middleware::class]], function(){  //this
    //api
    Route::get('/', function(){
        return 'Welcome to api routes';
    });

      //api/users
    Route::get('/users', function(){
        return 'Welcome to users api routes';
    },null,[AdminMiddleware::class]);

     //api/article
     Route::get('/articles', function(){
        return 'Welcome to article api routes';
    });
 });