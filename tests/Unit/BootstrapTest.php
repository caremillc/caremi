<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use Careminate\Config\Configuration;
use Careminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    public function testBootstrapReturnsBootedApplication(): void
    {
        $application = $this->bootstrap();

        self::assertTrue($application->isBooted());

        $application->terminate();
    }

    public function testBootstrapRegistersConfiguration(): void
    {
        $application = $this->bootstrap();

        $configuration = $application->container()->get(
            Configuration::class
        );

        self::assertInstanceOf(Configuration::class, $configuration);
        self::assertSame('Careminate', $configuration->string('app.name'));

        $application->terminate();
    }

    private function bootstrap(): Application
    {
        $environmentOverrides = [
            'APP_NAME' => 'Careminate',
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'false',
            'APP_PORT' => '8080',
        ];

        $environmentFile = dirname(__DIR__, 2) . '/.env.test-nonexistent';

        $application = require dirname(__DIR__, 2) . '/bootstrap/app.php';

        self::assertInstanceOf(Application::class, $application);

        return $application;
    }
}
