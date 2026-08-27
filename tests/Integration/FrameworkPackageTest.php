<?php

declare(strict_types=1);

namespace Careminate\Tests\Integration;

use Careminate\Container\Container;
use Careminate\Foundation\Version;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ApplicationDependency
{
}

final class FrameworkPackageTest extends TestCase
{
    public function testFrameworkPackageIsAutoloadableFromApplication(): void
    {
        self::assertTrue(class_exists(Version::class));
        self::assertSame('0.2.0-dev', Version::current());
    }

    public function testApplicationCanUsePsrContainer(): void
    {
        $container = new Container();
        $container->singleton(ApplicationDependency::class);

        self::assertInstanceOf(ContainerInterface::class, $container);
        self::assertInstanceOf(
            ApplicationDependency::class,
            $container->get(ApplicationDependency::class),
        );
    }
}
