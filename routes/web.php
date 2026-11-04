<?php

declare(strict_types=1);

use App\Http\ReferenceEndpoints;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Http\Routing\Router;

/**
 * @return Closure(Router): void
 */
return static function (Router $router): void {
    $router->get(
        '/',
        static fn (Request $request): Response => new Response(
            200,
            ['Content-Type' => 'text/plain; charset=utf-8'],
            'Careminate application',
        ),
        name: 'home',
    );

    $router->get(
        '/health',
        static fn (Request $request): Response => new Response(
            200,
            [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-store',
            ],
            'healthy',
        ),
        name: 'health',
    );

    $router->get(
        '/greet/{name}',
        ReferenceEndpoints::greeting(...),
        ['name' => '[A-Za-z]{1,40}'],
        'greeting.show',
    );

    $router->group(
        '/api',
        static function (Router $router): void {
            $router->get(
                '/status',
                static fn (Request $request): Response => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    '{"status":"ok"}',
                ),
                name: 'api.status',
            );

            $router->get(
                '/items/{id}',
                static fn (Request $request): Response => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(
                        ['id' => $request->routeParameter('id')],
                        JSON_THROW_ON_ERROR,
                    ),
                ),
                ['id' => '[0-9]+'],
                'api.items.show',
            );

            $router->add(
                'POST',
                '/echo',
                ReferenceEndpoints::echoJson(...),
                name: 'api.echo',
            );
        },
    );
};
