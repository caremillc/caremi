<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Reports\ReportManager;
use Careminate\Foundation\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('reports', function () {
            return new ReportManager();
        });
    }

    public static function provides(): array
    {
        return [
            'reports',
            ReportManager::class,
        ];
    }
}
