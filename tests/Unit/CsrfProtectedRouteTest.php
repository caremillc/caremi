<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Kernel;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Security\CsrfTokenManager;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CsrfProtectedRouteTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testMissingTokenBlocksController(): void
    {
        $calls = 0;
        $kernel = $this->kernelWithProtectedRoute($calls);

        try {
            $response = $kernel->handle(
                new Request('POST', '/__csrf_test')
            );

            self::assertSame(403, $response->status);
            self::assertSame(0, $calls);
            self::assertSame(
                PHP_SESSION_NONE,
                session_status()
            );
        } finally {
            $kernel->terminate();
        }
    }

    public function testInvalidTokenBlocksController(): void
    {
        $calls = 0;
        $kernel = $this->kernelWithProtectedRoute($calls);

        try {
            $response = $kernel->handle(
                new Request(
                    'POST',
                    '/__csrf_test',
                    [
                        'X-CSRF-Token' => str_repeat('0', 64),
                    ]
                )
            );

            self::assertSame(403, $response->status);
            self::assertSame(0, $calls);
        } finally {
            $kernel->terminate();
        }
    }

    public function testValidHeaderTokenAllowsController(): void
    {
        $token = $this->createSessionToken();

        $calls = 0;
        $kernel = $this->kernelWithProtectedRoute($calls);

        try {
            $response = $kernel->handle(
                new Request(
                    'POST',
                    '/__csrf_test',
                    ['X-CSRF-Token' => $token]
                )
            );

            self::assertSame(200, $response->status);
            self::assertSame('accepted', $response->body);
            self::assertSame(1, $calls);
        } finally {
            $kernel->terminate();
        }
    }

    public function testValidFormTokenAllowsController(): void
    {
        $token = $this->createSessionToken();

        $calls = 0;
        $kernel = $this->kernelWithProtectedRoute($calls);

        try {
            $response = $kernel->handle(
                new Request(
                    'POST',
                    '/__csrf_test',
                    [
                        'Content-Type' =>
                            'application/x-www-form-urlencoded',
                    ],
                    http_build_query([
                        '_token' => $token,
                        'title' => 'Example task',
                    ])
                )
            );

            self::assertSame(200, $response->status);
            self::assertSame(1, $calls);
        } finally {
            $kernel->terminate();
        }
    }

    private function createSessionToken(): string
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $sessions->start();

        try {
            $csrf = new CsrfTokenManager($sessions);

            return $csrf->token();
        } finally {
            if ($sessions->isActive()) {
                $sessions->close();
            }
        }
    }

    private function kernelWithProtectedRoute(
        int &$calls
    ): Kernel {
        $kernel = require dirname(__DIR__, 2)
            . '/bootstrap/http.php';

        self::assertInstanceOf(Kernel::class, $kernel);

        // Register only in this isolated test kernel.
        $kernel->router()->add(
            'POST',
            '/__csrf_test',
            static function (Request $request) use (
                &$calls
            ): Response {
                $calls++;

                return new Response(
                    200,
                    ['Content-Type' => 'text/plain'],
                    'accepted'
                );
            },
            name: 'test.csrf.protected'
        );

        return $kernel;
    }
}
