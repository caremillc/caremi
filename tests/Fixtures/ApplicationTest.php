<?php

declare(strict_types=1);

namespace Careminate\Tests\Feature;

use Careminate\Contracts\Application\ApplicationInterface;
use Careminate\Contracts\Providers\ServiceProviderInterface;
use Careminate\Foundation\Application;
use Careminate\Foundation\ApplicationState;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ApplicationTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $temporaryPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'careminate-'
            . bin2hex(random_bytes(8));

        if (!mkdir($temporaryPath, 0777, true) && !is_dir($temporaryPath)) {
            self::fail("Unable to create temporary directory [{$temporaryPath}].");
        }

        $resolvedPath = realpath($temporaryPath);

        if ($resolvedPath === false) {
            self::fail("Unable to resolve temporary directory [{$temporaryPath}].");
        }

        $this->basePath = $resolvedPath;
    }

    protected function tearDown(): void
    {
        @rmdir($this->basePath);
    }

    public function testApplicationExposesNormalizedPaths(): void
    {
        $app = new Application($this->basePath);

        self::assertSame(
            $this->basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php',
            $app->configPath('app.php'),
        );
        self::assertSame($this->basePath . DIRECTORY_SEPARATOR . 'public', $app->publicPath());
        self::assertSame($app, $app->get(ApplicationInterface::class));
    }

    public function testRelativePathOverridesAreResolvedFromTheBasePath(): void
    {
        $app = new Application($this->basePath);
        $app->usePath('storage', 'var/storage');

        self::assertSame(
            $this->basePath . DIRECTORY_SEPARATOR . 'var/storage',
            $app->storagePath(),
        );
    }

    public function testProvidersRegisterBeforeBootAndBootOnce(): void
    {
        $app = new Application($this->basePath);
        $provider = new RecordingProvider();
        $app->register($provider);
        $app->boot();
        $app->boot();

        self::assertSame(['register', 'boot'], $provider->events);
        self::assertTrue($app->isBooted());
    }

    public function testRegisteringProviderClassTwiceReturnsTheRegisteredInstance(): void
    {
        $app = new Application($this->basePath);

        $first = $app->register(RecordingProvider::class);
        $second = $app->register(RecordingProvider::class);

        self::assertSame($first, $second);
        self::assertSame(['register'], $first->events);
    }

    public function testLifecycleCallbacksAreInjectedAndOrdered(): void
    {
        $app = new Application($this->basePath);
        $events = [];
        $app->booting(static function (ApplicationInterface $app) use (&$events): void {
            $events[] = 'booting';
        });
        $app->booted(static function (ApplicationInterface $app) use (&$events): void {
            $events[] = 'booted';
        });
        $app->terminating(static function (ApplicationInterface $app) use (&$events): void {
            $events[] = 'terminating';
        });

        $app->boot();
        $app->terminate();

        self::assertSame(['booting', 'booted', 'terminating'], $events);
        self::assertSame(ApplicationState::Terminated, $app->state());
    }

    public function testAllTerminationCallbacksRunWhenOneFails(): void
    {
        $app = new Application($this->basePath);
        $events = [];
        $app->terminating(static function () use (&$events): void {
            $events[] = 'first';
        });
        $app->terminating(static function () use (&$events): void {
            $events[] = 'second';
            throw new RuntimeException('termination failed');
        });
        $app->boot();

        try {
            $app->terminate();
            self::fail('The first termination failure should be rethrown.');
        } catch (RuntimeException $exception) {
            self::assertSame('termination failed', $exception->getMessage());
            self::assertSame(['second', 'first'], $events);
            self::assertSame(ApplicationState::Terminated, $app->state());
        }
    }

    public function testFailedBootCanStillBeTerminated(): void
    {
        $app = new Application($this->basePath);
        $app->register(new FailingBootProvider());

        try {
            $app->boot();
            self::fail('Boot should have failed.');
        } catch (RuntimeException) {
            self::assertSame(ApplicationState::Failed, $app->state());
        }

        $app->terminate();
        self::assertSame(ApplicationState::Terminated, $app->state());
    }

    public function testFlushRestoresApplicationBindingsAndRegistrationState(): void
    {
        $app = new Application($this->basePath);
        $app->register(RecordingProvider::class);
        $app->flush();

        self::assertSame($app, $app->get(ApplicationInterface::class));
        self::assertSame(['register'], $app->register(RecordingProvider::class)->events);
    }

    public function testBootedApplicationCannotBeFlushed(): void
    {
        $app = new Application($this->basePath);
        $app->boot();

        $this->expectException(LogicException::class);
        $app->flush();
    }
}

final class RecordingProvider implements ServiceProviderInterface
{
    /** @var list<string> */
    public array $events = [];

    public function register(ApplicationInterface $app): void
    {
        $this->events[] = 'register';
    }

    public function boot(ApplicationInterface $app): void
    {
        $this->events[] = 'boot';
    }
}

final class FailingBootProvider implements ServiceProviderInterface
{
    public function register(ApplicationInterface $app): void
    {
    }

    public function boot(ApplicationInterface $app): void
    {
        throw new RuntimeException('boot failed');
    }
}