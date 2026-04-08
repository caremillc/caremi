<?php declare(strict_types=1);

use App\Http\Controllers\HomeController;


router()->get('/', function () {
    return "Careminate Framework Running";
});

router()->get('/home', HomeController::class);