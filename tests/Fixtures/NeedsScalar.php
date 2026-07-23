<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

final readonly class NeedsScalar
{
    public function __construct(public string $name)
    {
    }
}