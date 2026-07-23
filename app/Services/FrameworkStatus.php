<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ClockInterface;
use Careminate\Contracts\Application\ApplicationInterface;

final readonly class FrameworkStatus
{
    public function __construct(
        private ClockInterface $clock,
        private ApplicationInterface $app,
    ) {
    }

    /** @return array{name: string, status: string, time: string} */
    public function report(): array
    {
        return [
            'name' => 'Careminate',
            'status' => $this->app->isBooted() ? 'booted' : 'not-booted',
            'time' => $this->clock->now()->format(DATE_ATOM),
        ];
    }
}
