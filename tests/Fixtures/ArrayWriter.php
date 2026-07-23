<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

use Careminate\Tests\Fixtures\Writer;

final class ArrayWriter implements Writer
{
    public function write(string $message): string
    {
        return "written:{$message}";
    }
}