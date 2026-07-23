<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

use Careminate\Tests\Fixtures\CircularB;

final readonly class CircularA
{
    public function __construct(public CircularB $b)
    {
    }
}