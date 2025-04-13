<?php

use Careminate\Routing\Route;

Route::get('api', function () {
    echo  'Welcome to api routes';
});

Route::get('api/users', function () {
    echo 'Welcome to users api routes';
});