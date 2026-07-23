<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

final readonly class CircularB
{
    public function __construct(public CircularA $a)
    {
    }
}