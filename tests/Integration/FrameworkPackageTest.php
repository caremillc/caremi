<?php

declare(strict_types=1);

namespace Careminate\Tests\Integration;

use Careminate\Application\ApplicationBuilder;
use Careminate\Application\ApplicationState;
use Careminate\Application\Runtime\ConsoleRuntime;
use Careminate\Application\Runtime\RuntimeContext;
use Careminate\Application\Runtime\RuntimeResult;
use Careminate\Application\Runtime\RuntimeType;
use Careminate\Container\Container;
use Careminate\Contracts\Application\KernelInterface;
use Careminate\Foundation\Version;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Throwable;

final class FrameworkPackageTest extends TestCase
{
    public function testFrameworkPackageIsAutoloadableFromApplication(): void
    {
        self::assertTrue(class_exists(Version::class));
        self::assertSame('0.3.0-dev', Version::current());
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

    public function testApplicationBootsAndRunsConsoleLifecycle(): void
    {
        $output = '';

        $application = ApplicationBuilder::fromBasePath(
            'C:\\xampp\\htdocs\\caremi',
        )
            ->kernel(RuntimeType::Console, new RootConsoleKernel())
            ->build();

        $runtime = new ConsoleRuntime(
            ['caremi'],
            static function (string $message) use (&$output): void {
                $output .= $message;
            },
        );

        self::assertSame(0, $application->run($runtime));
        self::assertSame('Caremi booted', $output);
        self::assertSame(
            ApplicationState::Bootstrapped,
            $application->state(),
        );

        $application->terminate();

        self::assertSame(
            ApplicationState::Terminated,
            $application->state(),
        );
    }
}

final class ApplicationDependency
{
}

final class RootConsoleKernel implements KernelInterface
{
    public function handle(RuntimeContext $context): RuntimeResult
    {
        unset($context);

        return new RuntimeResult(0, 'Caremi booted');
    }

    public function terminate(
        RuntimeContext $context,
        ?RuntimeResult $result,
        ?Throwable $failure,
    ): void {
        unset($context, $result, $failure);
    }
}
