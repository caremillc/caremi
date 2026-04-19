<?php declare(strict_types=1);

namespace App\Http\Controllers;

class DashboardController
{
    public function index(): string
    {
        return 'Authenticated dashboard';
    }

    public function settings(): array
    {
        return [
            'section' => 'settings',
            'status' => 'ok',
        ];
    }
}