<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Foundation\Framework;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    public function testBootstrapReturnsFrameworkMetadata(): void
    {
        $result = require dirname(__DIR__, 2) . '/bootstrap/app.php';

        self::assertIsArray($result);
        self::assertSame(
            Framework::version(),
            $result['framework']
        );
        self::assertSame(
            PHP_VERSION,
            $result['php']
        );
    }

    public function testFrameworkIsAvailableThroughComposer(): void
    {
        self::assertTrue(class_exists(Framework::class));
        self::assertTrue(Framework::supportsRuntime());
    }
}
