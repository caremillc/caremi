<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

interface Writer
{
    public function write(string $message): string;
}
