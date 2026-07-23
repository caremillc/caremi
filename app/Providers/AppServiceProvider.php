<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\ClockInterface;
use App\Services\SystemClock;
use Careminate\Contracts\Application\ApplicationInterface;
use Careminate\Foundation\Providers\ServiceProvider;
use DateTimeZone;

final class AppServiceProvider extends ServiceProvider
{
    public function register(ApplicationInterface $app): void
    {
        $timezone = getenv('APP_TIMEZONE');

        $app->instance(
            DateTimeZone::class,
            new DateTimeZone(is_string($timezone) && $timezone !== '' ? $timezone : 'UTC'),
        );
        $app->singleton(ClockInterface::class, SystemClock::class);
    }
}
