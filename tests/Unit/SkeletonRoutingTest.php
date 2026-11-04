<?php

declare (strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Kernel;
use Careminate\Http\Request;
use Careminate\Http\Routing\Router;
use PHPUnit\Framework\TestCase;

final class SkeletonRoutingTest extends TestCase
{
    public function testRouteBootstrapContainsNamedApplicationRoutes(): void
    {
        $router = self::router();

        self::assertSame(
            [
                ['method' => 'GET', 'path' => '/', 'name' => 'home'],
                ['method' => 'POST', 'path' => '/api/echo', 'name' => 'api.echo'],
                ['method' => 'GET', 'path' => '/api/items/{id}', 'name' => 'api.items.show'],
                ['method' => 'GET', 'path' => '/api/status', 'name' => 'api.status'],
                ['method' => 'GET', 'path' => '/greet/{name}', 'name' => 'greeting.show'],
                ['method' => 'GET', 'path' => '/health', 'name' => 'health'],
            ],
            $router->registeredRoutes(),
        );

        self::assertSame(6, $router->count());
    }

    public function testRouteInspectionDoesNotFreezeRouter(): void
    {
        $router = self::router();

        self::assertFalse($router->isFrozen());
        // self::assertCount(4, $router->registeredRoutes());
        self::assertCount(6, $router->registeredRoutes());
        self::assertFalse($router->isFrozen());
    }

    public function testNamedDynamicUrlUsesRegisteredConstraint(): void
    {
        self::assertSame(
            '/api/items/42',
            self::router()->url(
                'api.items.show',
                ['id' => 42],
            ),
        );
    }

    public function testHealthRouteDispatchesSuccessfully(): void
    {
        $response = self::router()->dispatch(
            new Request('GET', '/health'),
        );

        self::assertSame(200, $response->status);
        self::assertSame('healthy', $response->body);
    }

    public function testDynamicRouteReceivesDecodedParameter(): void
    {
        $response = self::router()->dispatch(
            new Request('GET', '/api/items/42'),
        );

        self::assertSame(200, $response->status);

        self::assertSame(
            ['id' => '42'],
            json_decode(
                $response->body,
                true,
                flags: JSON_THROW_ON_ERROR,
            ),
        );
    }

    public function testConstraintRejectsInvalidDynamicSegment(): void
    {
        $response = self::router()->dispatch(
            new Request('GET', '/api/items/not-numeric'),
        );

        self::assertSame(404, $response->status);
    }

    public function testHttpBootstrapSharesCliRouteSnapshot(): void
    {
        $kernel = self::kernel();

        try {
            self::assertSame(
                self::router()->registeredRoutes(),
                $kernel->router()->registeredRoutes(),
            );
        } finally {
            $kernel->terminate();
        }
    }

    public function testHttpKernelDispatchesActualApplicationRoute(): void
    {
        $kernel = self::kernel();

        try {
            $response = $kernel->handle(
                new Request('GET', '/api/status'),
            );

            self::assertSame(200, $response->status);
            self::assertSame(
                '{"status":"ok"}',
                $response->body,
            );

            self::assertTrue(
                $kernel->router()->isFrozen(),
            );
        } finally {
            $kernel->terminate();
        }
    }

    public function testRouterCreatedForEachBootstrapIsIndependent(): void
    {
        $first  = self::router();
        $second = self::router();

        self::assertNotSame($first, $second);

        self::assertSame(
            $first->registeredRoutes(),
            $second->registeredRoutes(),
        );
    }

    private static function router(): Router
    {
        $router = require dirname(__DIR__, 2)
            . '/bootstrap/routes.php';

        self::assertInstanceOf(Router::class, $router);

        return $router;
    }

    private static function kernel(): Kernel
    {
        // These values are inherited by bootstrap/app.php through
        // bootstrap/http.php in this PHP include scope.
        $environmentOverrides = [
            'APP_NAME'  => 'Careminate',
            'APP_ENV'   => 'testing',
            'APP_DEBUG' => 'false',
            'APP_PORT'  => '8080',
        ];

        $environmentFile = dirname(__DIR__, 2)
            . '/.env.test-nonexistent';

        $kernel = require dirname(__DIR__, 2)
            . '/bootstrap/http.php';

        self::assertInstanceOf(Kernel::class, $kernel);

        return $kernel;
    }
}
