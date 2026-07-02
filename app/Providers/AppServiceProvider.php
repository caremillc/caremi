<?php

declare(strict_types=1);

namespace App\Providers;

use Careminate\Foundation\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('app.name', fn () => 'Careminate');
    }

    public function boot(): void
    {
        //
    }
}