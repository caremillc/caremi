<?php declare(strict_types=1);

namespace App\Http\Controllers;


class HomeController extends Controller
{
    public function __invoke()
    {
        return $this->response(
            "Welcome to Careminate Framework home page!"
        );
    }
}