<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

use Careminate\Tests\Fixtures\Writer;

final class JsonWriter implements Writer
{
    public function write(string $message): string
    {
        return json_encode(['message' => $message], JSON_THROW_ON_ERROR);
    }
}