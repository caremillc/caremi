<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

abstract class StaticHandler
{
    public static function handle(Writer $writer, string $message): string
    {
        return $writer->write($message);
    }
}