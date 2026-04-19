<?php declare(strict_types=1);

namespace App\Http\Controllers;

class HomeController
{
    public function index(): string
    {
        return 'Welcome to Careminate Framework Home Page';
    }

    public function show(string $id): array
    {
        return [
            'user_id' => $id,
            'message' => 'User route parameter resolved successfully.',
        ];
    }
}
