<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Emission\BufferedResponseOutput;
use Careminate\Http\Emission\ResponseEmitter;
use Careminate\Http\Kernel;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Http\Routing\Router;
use Careminate\Http\Runtime\HttpRunner;
use Careminate\Http\ServerRequestFactory;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ReferenceHttpEndpointsTest extends TestCase
{
    public function testCliAndHttpBootstrapSeeSameSixRoutes(): void
    {
        $router = self::router();
        $kernel = self::kernel();

        try {
            self::assertSame(6, $router->count());

            self::assertSame(
                $router->registeredRoutes(),
                $kernel->router()->registeredRoutes(),
            );

            self::assertSame(
                '/greet/Ada',
                $router->url(
                    'greeting.show',
                    ['name' => 'Ada'],
                ),
            );

            self::assertSame(
                '/api/echo',
                $router->url('api.echo'),
            );
        } finally {
            $kernel->terminate();
        }
    }

    public function testHealthRequestPassesThroughHttpRunnerAndEmitter(): void
    {
        $kernel = self::kernel();
        $output = new BufferedResponseOutput();

        $runner = new HttpRunner(
            new ServerRequestFactory(),
            $kernel,
            new ResponseEmitter($output),
        );

        try {
            $response = $runner->run([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/health',
            ]);

            self::assertSame(200, $response->status);
            self::assertSame('healthy', $response->body);
            self::assertSame(200, $output->status());
            self::assertSame('healthy', $output->body());

            self::assertSame(
                'no-store',
                $response->header('Cache-Control'),
            );
        } finally {
            $kernel->terminate();
        }
    }

    public function testGreetingReturnsJson(): void
    {
        $response = self::router()->dispatch(
            new Request('GET', '/greet/Ada'),
        );

        self::assertSame(200, $response->status);

        self::assertSame(
            'application/json; charset=utf-8',
            $response->header('Content-Type'),
        );

        self::assertSame(
            ['greeting' => 'Hello, Ada!'],
            self::json($response),
        );
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidGreetingNames(): iterable
    {
        yield 'numeric' => ['/greet/123'];

        yield 'punctuation' => ['/greet/Ada!'];

        yield 'too long' => [
            '/greet/' . str_repeat('a', 41),
        ];
    }

    #[DataProvider('invalidGreetingNames')]
    public function testGreetingConstraintRejectsInvalidNames(
        string $path,
    ): void {
        $response = self::router()->dispatch(
            new Request('GET', $path),
        );

        self::assertSame(404, $response->status);
    }

    public function testValidJsonPostThroughHttpRunner(): void
    {
        $kernel = self::kernel();
        $output = new BufferedResponseOutput();

        $runner = new HttpRunner(
            new ServerRequestFactory(),
            $kernel,
            new ResponseEmitter($output),
        );

        try {
            $response = $runner->run(
                [
                    'REQUEST_METHOD' => 'POST',
                    'REQUEST_URI' => '/api/echo',
                    'CONTENT_TYPE' => 'application/json; charset=utf-8',
                ],
                '{"message":"Hello Careminate"}',
            );

            self::assertSame(200, $response->status);
            self::assertSame(200, $output->status());

            self::assertSame(
                $response->body,
                $output->body(),
            );

            self::assertSame(
                ['message' => 'Hello Careminate'],
                self::json($response),
            );

            self::assertSame(
                'nosniff',
                $response->header('X-Content-Type-Options'),
            );
        } finally {
            $kernel->terminate();
        }
    }

    public function testUnicodeMessageRoundTripsWithoutChangingContents(): void
    {
        $response = self::echo(
            '{"message":"Café"}',
        );

        self::assertSame(200, $response->status);

        self::assertSame(
            ['message' => 'Café'],
            self::json($response),
        );
    }

    /**
     * @return iterable<string, array{
     *     string,
     *     ?string,
     *     int,
     *     string
     * }>
     */
    public static function invalidJsonRequests(): iterable
    {
        yield 'missing content type' => [
            '{"message":"Hi"}',
            null,
            415,
            'unsupported_media_type',
        ];

        yield 'wrong content type' => [
            '{"message":"Hi"}',
            'text/plain',
            415,
            'unsupported_media_type',
        ];

        yield 'unsupported charset' => [
            '{"message":"Hi"}',
            'application/json; charset=latin1',
            415,
            'unsupported_media_type',
        ];

        yield 'malformed JSON' => [
            '{"message":',
            'application/json',
            400,
            'invalid_json',
        ];

        yield 'empty request' => [
            '',
            'application/json',
            400,
            'invalid_json',
        ];

        yield 'array root' => [
            '[]',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'missing message' => [
            '{}',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'extra property' => [
            '{"message":"Hi","role":"admin"}',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'numeric message' => [
            '{"message":42}',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'blank message' => [
            '{"message":"   "}',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'control character' => [
            '{"message":"Hi\nthere"}',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'message limit' => [
            '{"message":"' . str_repeat('x', 121) . '"}',
            'application/json',
            422,
            'invalid_message',
        ];

        yield 'body limit' => [
            '{"message":"' . str_repeat('x', 1025) . '"}',
            'application/json',
            413,
            'body_too_large',
        ];
    }

    #[DataProvider('invalidJsonRequests')]
    public function testJsonEndpointRejectsInvalidRequests(
        string $body,
        ?string $contentType,
        int $status,
        string $code,
    ): void {
        $response = self::echo(
            $body,
            $contentType,
        );

        self::assertSame($status, $response->status);

        self::assertSame(
            'no-store',
            $response->header('Cache-Control'),
        );

        $decoded = self::json($response);
        $error = $decoded['error'] ?? null;

        if (! is_array($error)) {
            throw new \UnexpectedValueException(
                'Expected an error object.',
            );
        }

        self::assertSame(
            $code,
            $error['code'] ?? null,
        );

        self::assertArrayHasKey('message', $error);
        self::assertArrayNotHasKey('message', $decoded);
    }

    public function testPostOnlyEndpointRejectsGet(): void
    {
        $response = self::router()->dispatch(
            new Request('GET', '/api/echo'),
        );

        self::assertSame(405, $response->status);

        self::assertStringContainsString(
            'POST',
            $response->header('Allow') ?? '',
        );
    }

    public function testKernelCanTerminateAfterRequest(): void
    {
        $kernel = self::kernel();

        try {
            $response = $kernel->handle(
                new Request('GET', '/health'),
            );

            self::assertSame(200, $response->status);
        } finally {
            $kernel->terminate();
        }

        $this->expectException(LogicException::class);

        $kernel->handle(
            new Request('GET', '/health'),
        );
    }

    private static function echo(
        string $body,
        ?string $contentType = 'application/json',
    ): Response {
        $headers = $contentType === null
            ? []
            : ['Content-Type' => $contentType];

        return self::router()->dispatch(
            new Request(
                'POST',
                '/api/echo',
                $headers,
                $body,
            ),
        );
    }

    /**
     * @return array<mixed, mixed>
     */
    private static function json(Response $response): array
    {
        $decoded = json_decode(
            $response->body,
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        if (! is_array($decoded)) {
            throw new \UnexpectedValueException(
                'Expected JSON object.',
            );
        }

        return $decoded;
    }

    private static function router(): Router
    {
        $router = require dirname(__DIR__, 2)
            . '/bootstrap/routes.php';

        if (! $router instanceof Router) {
            throw new \UnexpectedValueException(
                'Expected Router.',
            );
        }

        return $router;
    }

    private static function kernel(): Kernel
    {
        $environmentOverrides = [
            'APP_NAME' => 'Careminate',
            'APP_ENV' => 'testing',
            'APP_DEBUG' => 'false',
            'APP_PORT' => '8080',
        ];

        $externalEnvironment = [];

        $environmentFile = dirname(__DIR__, 2)
            . '/.env.day22-nonexistent';

        $kernel = require dirname(__DIR__, 2)
            . '/bootstrap/http.php';

        if (! $kernel instanceof Kernel) {
            throw new \UnexpectedValueException(
                'Expected Kernel.',
            );
        }

        return $kernel;
    }
}
