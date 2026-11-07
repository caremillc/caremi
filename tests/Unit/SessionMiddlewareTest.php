<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Middleware\SessionMiddleware;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SessionMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testStartsAndClosesSession(): void
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $middleware = new SessionMiddleware($sessions);

        $request = $this->request();
        $response = $this->response();

        $called = false;

        $result = $middleware->process(
            $request,
            function (Request $received) use (
                $sessions,
                $request,
                $response,
                &$called
            ): Response {
                $called = true;

                self::assertSame($request, $received);
                self::assertTrue($sessions->isActive());

                $sessions->set('task_filter', 'pending');

                self::assertSame(
                    'pending',
                    $sessions->get('task_filter')
                );

                return $response;
            }
        );

        self::assertTrue($called);
        self::assertSame($response, $result);
        self::assertFalse($sessions->isActive());
    }

    public function testClosesSessionWhenHandlerThrows(): void
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $middleware = new SessionMiddleware($sessions);

        $caught = null;

        try {
            $middleware->process(
                $this->request(),
                function (Request $request) use ($sessions): Response {
                    self::assertTrue($sessions->isActive());

                    throw new RuntimeException(
                        'Controller failure'
                    );
                }
            );
        } catch (RuntimeException $exception) {
            $caught = $exception;
        }

        self::assertInstanceOf(
            RuntimeException::class,
            $caught
        );

        self::assertSame(
            'Controller failure',
            $caught->getMessage()
        );

        self::assertFalse($sessions->isActive());
    }

    public function testDoesNotCloseTwiceAfterLogout(): void
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $middleware = new SessionMiddleware($sessions);

        $response = $this->response();

        $result = $middleware->process(
            $this->request(),
            function (Request $request) use (
                $sessions,
                $response
            ): Response {
                $sessions->set('user_id', 42);
                $sessions->logout();

                self::assertFalse(
                    $sessions->isActive()
                );

                return $response;
            }
        );

        self::assertSame($response, $result);
        self::assertFalse($sessions->isActive());
    }

    private function request(): Request
    {
        return new ReflectionClass(Request::class)
            ->newInstanceWithoutConstructor();
    }

    private function response(): Response
    {
        return new ReflectionClass(Response::class)
            ->newInstanceWithoutConstructor();
    }
}
