<?php

declare(strict_types=1);

namespace Careminate\Tests\Feature;

use Careminate\Container\Container;
use Careminate\Container\Exceptions\BindingResolutionException;
use Careminate\Container\Exceptions\CircularDependencyException;
use Careminate\Container\Exceptions\EntryNotFoundException;
use Careminate\Contracts\Container\ContainerInterface;
use Careminate\Tests\Fixtures\ArrayWriter;
use Careminate\Tests\Fixtures\CircularA;
use Careminate\Tests\Fixtures\ContextualReportService;
use Careminate\Tests\Fixtures\Counter;
use Careminate\Tests\Fixtures\JsonWriter;
use Careminate\Tests\Fixtures\NeedsScalar;
use Careminate\Tests\Fixtures\ReportService;
use Careminate\Tests\Fixtures\RequestContext;
use Careminate\Tests\Fixtures\StaticHandler;
use Careminate\Tests\Fixtures\Writer;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testItAutowiresConcreteClasses(): void
    {
        self::assertInstanceOf(Counter::class, (new Container())->make(Counter::class));
    }

    public function testItResolvesInterfacesThroughBindings(): void
    {
        $container = new Container();
        $container->bind(Writer::class, ArrayWriter::class);

        self::assertInstanceOf(ArrayWriter::class, $container->make(ReportService::class)->writer);
    }

    public function testSingletonsReturnTheSameInstance(): void
    {
        $container = new Container();
        $container->singleton(Counter::class);

        self::assertSame($container->make(Counter::class), $container->make(Counter::class));
    }

    public function testTransientBindingsReturnDifferentInstances(): void
    {
        $container = new Container();
        $container->bind(Counter::class);

        self::assertNotSame($container->make(Counter::class), $container->make(Counter::class));
    }

    public function testNamedRuntimeParametersResolveScalars(): void
    {
        $service = (new Container())->make(NeedsScalar::class, ['name' => 'Careminate']);

        self::assertSame('Careminate', $service->name);
    }

    public function testCallInjectsDependenciesAndNamedParameters(): void
    {
        $container = new Container();
        $container->bind(Writer::class, ArrayWriter::class);

        $result = $container->call(
            static fn (Writer $writer, string $message): string => $writer->write($message),
            ['message' => 'hello'],
        );

        self::assertSame('written:hello', $result);
    }

    public function testStaticCallableStringsAreSupportedWithoutInstantiatingTheClass(): void
    {
        $container = new Container();
        $container->bind(Writer::class, ArrayWriter::class);

        self::assertSame(
            'written:hello',
            $container->call(StaticHandler::class . '::handle', ['message' => 'hello']),
        );
    }

    public function testPsrGetThrowsNotFoundForUnknownEntry(): void
    {
        $this->expectException(EntryNotFoundException::class);
        (new Container())->get('missing.entry');
    }

    public function testAliasesResolveToTheirAbstract(): void
    {
        $container = new Container();
        $container->singleton(Counter::class);
        $container->alias(Counter::class, 'counter');

        self::assertSame($container->make(Counter::class), $container->make('counter'));
    }

    public function testInvalidAliasDoesNotCorruptAnExistingAlias(): void
    {
        $container = new Container();
        $container->singleton(Counter::class);
        $container->alias(Counter::class, 'counter');

        try {
            $container->alias('counter', Counter::class);
            self::fail('A circular alias should have been rejected.');
        } catch (BindingResolutionException) {
            self::assertSame($container->make(Counter::class), $container->make('counter'));
        }
    }

    public function testCircularConstructorDependenciesAreDetected(): void
    {
        $this->expectException(CircularDependencyException::class);
        (new Container())->make(CircularA::class);
    }

    public function testCircularFactoryDependenciesAreDetected(): void
    {
        $container = new Container();
        $container->bind('loop', static fn (Container $container): mixed => $container->make('loop'));

        $this->expectException(CircularDependencyException::class);
        $container->make('loop');
    }

    public function testContextualBindingOverridesGlobalBinding(): void
    {
        $container = new Container();
        $container->bind(Writer::class, ArrayWriter::class);
        $container->when(ContextualReportService::class)->needs(Writer::class)->give(JsonWriter::class);

        self::assertInstanceOf(JsonWriter::class, $container->make(ContextualReportService::class)->writer);
        self::assertInstanceOf(ArrayWriter::class, $container->make(ReportService::class)->writer);
    }

    public function testScopedServicesAreSharedOnlyInsideScope(): void
    {
        $container = new Container();
        $container->scoped(RequestContext::class);
        $container->beginScope('request-1');
        $first = $container->make(RequestContext::class);
        $second = $container->make(RequestContext::class);
        $container->endScope();

        $container->beginScope('request-2');
        $third = $container->make(RequestContext::class);
        $container->endScope();

        self::assertSame($first, $second);
        self::assertNotSame($first, $third);
    }

    public function testScopedFactoriesAreNotExecutedOutsideAnActiveScope(): void
    {
        $container = new Container();
        $executions = 0;
        $container->scoped(RequestContext::class, static function () use (&$executions): RequestContext {
            $executions++;

            return new RequestContext();
        });

        try {
            $container->make(RequestContext::class);
            self::fail('Scoped resolution should have failed.');
        } catch (BindingResolutionException) {
            self::assertSame(0, $executions);
        }
    }

    public function testSharedBindingsRejectRuntimeParameters(): void
    {
        $container = new Container();
        $container->singleton(NeedsScalar::class);

        $this->expectException(BindingResolutionException::class);
        $container->make(NeedsScalar::class, ['name' => 'Careminate']);
    }

    public function testFlushRestoresCoreContainerBindings(): void
    {
        $container = new Container();
        $container->singleton(Counter::class);
        $container->flush();

        self::assertFalse($container->bound(Counter::class));
        self::assertSame($container, $container->get(ContainerInterface::class));
    }
}
