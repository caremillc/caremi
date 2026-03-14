<?php declare(strict_types=1);

use Careminate\Routing\Route;

Route::get('/', function () {
    return 'Careminate Framework Running';
});

Route::get('/view', function () {
    return response()->view('home', [
        'title' => 'Careminate',
        'loggedIn' => true,
        'user' => 'John',
        'users' => ['Alice', 'Bob', 'Charlie']
    ]);
});

