<?php

declare(strict_types=1);

namespace Careminate\Tests\Integration;

use Careminate\Foundation\Version;
use PHPUnit\Framework\TestCase;

final class FrameworkPackageTest extends TestCase
{
    public function testFrameworkPackageIsAutoloadableFromApplication(): void
    {
        self::assertTrue(class_exists(Version::class));
        self::assertSame('0.1.0-dev', Version::current());
    }
}