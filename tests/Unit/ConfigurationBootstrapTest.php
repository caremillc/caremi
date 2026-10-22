<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use Careminate\Config\Configuration;
use Careminate\Foundation\Application;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConfigurationBootstrapTest extends TestCase
{
    public function testExplicitOverridesWin(): void
    {
        $application = $this->bootstrap(
            [
                'APP_NAME' => 'Override',
                'APP_ENV' => 'testing',
            ],
            [
                'APP_NAME' => 'External',
            ]
        );

        $configuration = $this->configuration($application);

        self::assertSame('Override', $configuration->string('app.name'));

        $application->terminate();
    }

    public function testExternalEnvironmentOverridesDefaults(): void
    {
        $application = $this->bootstrap(
            ['APP_ENV' => 'testing'],
            ['APP_NAME' => 'External']
        );

        self::assertSame(
            'External',
            $this->configuration($application)->string('app.name')
        );

        $application->terminate();
    }

    public function testDefaultsAllowBootstrapWithoutEnvFile(): void
    {
        $application = $this->bootstrap();

        $configuration = $this->configuration($application);

        self::assertSame('production', $configuration->string('app.environment'));
        self::assertFalse($configuration->boolean('app.debug'));
        self::assertTrue($application->isBooted());

        $application->terminate();
    }

    public function testInvalidEnvironmentIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->bootstrap(['APP_ENV' => 'unknown']);
    }

    public function testInvalidPortIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->bootstrap(['APP_PORT' => '70000']);
    }

    public function testProductionDebugIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->bootstrap(['APP_DEBUG' => 'true']);
    }

    public function testInvalidBooleanIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->bootstrap(['APP_DEBUG' => 'perhaps']);
    }

    public function testConfigurationIsSharedByContainer(): void
    {
        $application = $this->bootstrap();

        $first = $application->container()->get(Configuration::class);
        $second = $application->container()->get(Configuration::class);

        self::assertSame($first, $second);

        $application->terminate();
    }

    /**
     * @param array<string, string> $overrides
     * @param array<string, string> $external
     */
    private function bootstrap(
        array $overrides = [],
        array $external = []
    ): Application {
        $environmentOverrides = $overrides;
        $externalEnvironment = $external;

        $environmentFile = dirname(__DIR__, 2) . '/.env.test-nonexistent';

        $application = require dirname(__DIR__, 2) . '/bootstrap/app.php';

        self::assertInstanceOf(Application::class, $application);

        return $application;
    }

    private function configuration(Application $application): Configuration
    {
        $configuration = $application->container()->get(
            Configuration::class
        );

        self::assertInstanceOf(Configuration::class, $configuration);

        return $configuration;
    }
}
