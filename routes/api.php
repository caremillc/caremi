<?php

use Careminate\Routing\Route;
use App\Http\Middlewares\Middleware;
use App\Http\Middlewares\AdminMiddleware;
use Careminate\FrameworkSetting;

Route::group(['prefix'=>'/api/','middleware'=>[Middleware::class]], function(){  //this
    //api
    Route::get('/', function(){
        FrameworkSetting::setLocale('ar');
        return FrameworkSetting::getLocale();
        // return  Session::get('locale');
   });

      //api/users
    Route::get('/users', function(){
        return 'Welcome to users api routes';
    },[AdminMiddleware::class]);

     //api/article
     Route::get('/articles', function(){
        return 'Welcome to article api routes';
    });
 });