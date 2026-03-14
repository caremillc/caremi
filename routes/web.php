<?php declare(strict_types=1);

use Careminate\Routing\Route;

Route::get('/', function () {
    return 'Careminate Framework Running';
});

Route::get('/view', function () {
    return response()->view('auth.register');
});

Route::post('/register', function () {

    request()->validate([
        'name' => 'required|min:3',
        'email' => 'required|email',
        'password' => 'required|min:8'
    ]);

    return "User created!";
});