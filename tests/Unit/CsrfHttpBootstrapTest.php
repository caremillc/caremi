<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Kernel;
use Careminate\Http\Request;
use Careminate\Http\Response;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CsrfHttpBootstrapTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testHealthEndpointRemainsAccessible(): void
    {
        $response = $this->dispatch(
            new Request('GET', '/health')
        );

        self::assertSame(200, $response->status);
        self::assertSame('healthy', $response->body);
        self::assertSame(
            PHP_SESSION_NONE,
            session_status()
        );
    }

    public function testUnsafeRequestWithoutTokenIsRejected(): void
    {
        $response = $this->dispatch(
            new Request(
                'POST',
                '/tasks',
                [
                    'Content-Type' =>
                        'application/x-www-form-urlencoded',
                ],
                'title=Example'
            )
        );

        self::assertSame(403, $response->status);
        self::assertSame(
            'Invalid CSRF token.',
            $response->body
        );

        self::assertSame(
            PHP_SESSION_NONE,
            session_status()
        );
    }

    public function testStatelessJsonEchoRemainsExempt(): void
    {
        $response = $this->dispatch(
            new Request(
                'POST',
                '/api/echo',
                [
                    'Content-Type' => 'application/json',
                ],
                '{}'
            )
        );

        // The endpoint may reject the JSON payload
        // through its own validation, but CSRF protection
        // must not reject this explicitly exempt route.
        self::assertNotSame(
            403,
            $response->status
        );

        self::assertSame(
            PHP_SESSION_NONE,
            session_status()
        );
    }

    public function testCsrfExemptionRequiresExactPath(): void
    {
        $response = $this->dispatch(
            new Request(
                'POST',
                '/api/echo/',
                [
                    'Content-Type' => 'application/json',
                ],
                '{}'
            )
        );

        self::assertSame(403, $response->status);
    }

    private function dispatch(Request $request): Response
    {
        $kernel = require dirname(__DIR__, 2)
            . '/bootstrap/http.php';

        self::assertInstanceOf(Kernel::class, $kernel);

        try {
            return $kernel->handle($request);
        } finally {
            $kernel->terminate();
        }
    }
}
