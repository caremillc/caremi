<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Middleware\CsrfMiddleware;
use Careminate\Http\Middleware\Pipeline;
use Careminate\Http\Middleware\SessionMiddleware;
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
final class CsrfMiddlewareTest extends TestCase
{
    private SessionManager $sessions;
    private CsrfTokenManager $csrf;
    private Pipeline $pipeline;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $this->csrf = new CsrfTokenManager(
            $this->sessions
        );

        $this->pipeline = new Pipeline();

        // Ordering is security-critical.
        $this->pipeline->add(
            new SessionMiddleware($this->sessions)
        );

        $this->pipeline->add(
            new CsrfMiddleware($this->csrf)
        );
    }

    protected function tearDown(): void
    {
        if ($this->sessions->isActive()) {
            $this->sessions->close();
        }

        parent::tearDown();
    }

    public function testGetRequestDoesNotRequireToken(): void
    {
        $response = $this->dispatch(
            new Request('GET', '/tasks')
        );

        self::assertSame(200, $response->status);
    }

    public function testPostWithoutTokenIsRejected(): void
    {
        $response = $this->dispatch(
            new Request('POST', '/tasks')
        );

        self::assertSame(403, $response->status);
        self::assertSame('Invalid CSRF token.', $response->body);
    }

    public function testMalformedTokenIsRejected(): void
    {
        $response = $this->dispatch(
            new Request(
                'POST',
                '/tasks',
                ['X-CSRF-Token' => 'invalid']
            )
        );

        self::assertSame(403, $response->status);
    }

    public function testValidHeaderTokenIsAccepted(): void
    {
        $this->sessions->start();
        $token = $this->csrf->token();
        $this->sessions->close();

        $response = $this->dispatch(
            new Request(
                'POST',
                '/tasks',
                ['X-CSRF-Token' => $token]
            )
        );

        self::assertSame(200, $response->status);
    }

    public function testValidFormTokenIsAccepted(): void
    {
        $this->sessions->start();
        $token = $this->csrf->token();
        $this->sessions->close();

        $response = $this->dispatch(
            new Request(
                'POST',
                '/tasks',
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
    }

    public function testInvalidTokenNeverReachesHandler(): void
    {
        $executed = false;

        $response = $this->pipeline->handle(
            new Request('DELETE', '/tasks/1'),
            function (Request $request) use (&$executed): Response {
                $executed = true;

                return new Response(200);
            }
        );

        self::assertFalse($executed);
        self::assertSame(403, $response->status);
        self::assertFalse($this->sessions->isActive());
    }

    private function dispatch(Request $request): Response
    {
        return $this->pipeline->handle(
            $request,
            static fn (Request $request): Response =>
            new Response(200, [], 'accepted')
        );
    }

    public function testExactStatelessApiExceptionIsAllowed(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add(
            new SessionMiddleware($this->sessions)
        );

        $pipeline->add(
            new CsrfMiddleware(
                $this->csrf,
                isExempt: static fn (Request $request): bool =>
                $request->method === 'POST'
                    && $request->path() === '/api/echo'
            )
        );

        $response = $pipeline->handle(
            new Request('POST', '/api/echo'),
            static fn (Request $request): Response =>
            new Response(200, [], 'accepted')
        );

        self::assertSame(200, $response->status);
    }

    public function testExceptionDoesNotExposeOtherApiRoutes(): void
    {
        $pipeline = new Pipeline();

        $pipeline->add(
            new SessionMiddleware($this->sessions)
        );

        $pipeline->add(
            new CsrfMiddleware(
                $this->csrf,
                isExempt: static fn (Request $request): bool =>
                $request->method === 'POST'
                    && $request->path() === '/api/echo'
            )
        );

        $executed = false;

        $response = $pipeline->handle(
            new Request('POST', '/api/tasks'),
            static function (Request $request) use (
                &$executed
            ): Response {
                $executed = true;

                return new Response(200);
            }
        );

        self::assertSame(403, $response->status);
        self::assertFalse($executed);
    }

    public function testInvalidHeaderCannotBeBypassedByValidFormToken(): void
    {
        $this->sessions->start();

        $validToken = $this->csrf->token();

        $this->sessions->close();

        $response = $this->dispatch(
            new Request(
                'POST',
                '/tasks',
                [
                    'Content-Type' =>
                    'application/x-www-form-urlencoded',
                    'X-CSRF-Token' => 'invalid',
                ],
                http_build_query([
                    '_token' => $validToken,
                ])
            )
        );

        self::assertSame(403, $response->status);
    }
}
