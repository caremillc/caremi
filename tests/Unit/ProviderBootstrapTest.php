<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ApplicationIdentity;
use Careminate\Config\Configuration;
use Careminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

final class ProviderBootstrapTest extends TestCase
{
    public function testConfiguredProviderRegistersApplicationIdentity(): void
    {
        $application = $this->bootstrap([
            'APP_NAME' => 'Careminate Provider Test',
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'false',
        ]);

        $identity = $application->container()->get(
            ApplicationIdentity::class
        );

        self::assertInstanceOf(
            ApplicationIdentity::class,
            $identity
        );

        self::assertSame(
            'Careminate Provider Test',
            $identity->name
        );

        self::assertSame('testing', $identity->environment);
    }

    public function testApplicationIdentityIsShared(): void
    {
        $application = $this->bootstrap([
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'false',
        ]);

        $first = $application->container()->get(
            ApplicationIdentity::class
        );

        $second = $application->container()->get(
            ApplicationIdentity::class
        );

        self::assertSame($first, $second);
    }

    public function testProviderUsesValidatedConfiguration(): void
    {
        $application = $this->bootstrap([
            'APP_NAME' => 'Configured Application',
            'APP_ENV' => 'staging',
            'APP_DEBUG' => 'false',
        ]);

        $configuration = $application->container()->get(
            Configuration::class
        );

        $identity = $application->container()->get(
            ApplicationIdentity::class
        );

        self::assertInstanceOf(
            Configuration::class,
            $configuration
        );

        self::assertInstanceOf(
            ApplicationIdentity::class,
            $identity
        );

        self::assertSame(
            $configuration->string('app.name'),
            $identity->name
        );

        self::assertSame(
            $configuration->string('app.environment'),
            $identity->environment
        );

        self::assertTrue($application->isBooted());
    }

    /**
     * @param array<string, string> $overrides
     */
    private function bootstrap(array $overrides): Application
    {
        $environmentOverrides = $overrides;
        $externalEnvironment = [];
        $environmentFile = dirname(__DIR__, 2)
            . '/.env.provider-test-missing';

        /** @var Application $application */
        $application = require dirname(__DIR__, 2)
            . '/bootstrap/app.php';

        return $application;
    }
}
