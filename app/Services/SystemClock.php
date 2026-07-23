<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ClockInterface;
use DateTimeImmutable;
use DateTimeZone;

final readonly class SystemClock implements ClockInterface
{
    public function __construct(private DateTimeZone $timezone)
    {
    }

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }
}