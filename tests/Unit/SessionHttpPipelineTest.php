<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Http\Middleware\Middleware;
use Careminate\Http\Middleware\Pipeline;
use Careminate\Http\Middleware\SessionMiddleware;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use Closure;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SessionHttpPipelineTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testRealHttpRequestUsesSessionMiddleware(): void
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $pipeline = new Pipeline();

        $pipeline->add(
            new SessionMiddleware($sessions)
        );

        $request = new Request(
            method: 'GET',
            target: '/health',
        );

        $expected = new Response(
            status: 200,
            body: 'healthy',
        );

        $response = $pipeline->handle(
            $request,
            function (Request $received) use (
                $sessions,
                $request,
                $expected
            ): Response {
                self::assertSame(
                    $request,
                    $received
                );

                self::assertTrue(
                    $sessions->isActive()
                );

                $sessions->set('visited', true);

                self::assertTrue(
                    $sessions->get('visited')
                );

                return $expected;
            }
        );

        self::assertSame(
            $expected,
            $response
        );

        self::assertSame(
            'healthy',
            $response->body
        );

        self::assertFalse(
            $sessions->isActive()
        );
    }

    public function testMiddlewareExecutionOrder(): void
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $pipeline = new Pipeline();

        $events = new \ArrayObject();

        $pipeline->add(
            new SessionMiddleware($sessions)
        );

        $pipeline->add(
            new class ($sessions, $events) implements Middleware {
                /**
                 * @param \ArrayObject<int, string> $events
                 */
                public function __construct(
                    private readonly SessionManager $sessions,
                    private readonly \ArrayObject $events,
                ) {
                }

                /**
                 * @param Closure(Request): Response $next
                 */
                public function process(
                    Request $request,
                    Closure $next,
                ): Response {
                    $this->events->append('before');

                    if (! $this->sessions->isActive()) {
                        throw new RuntimeException(
                            'Session not started.'
                        );
                    }

                    $response = $next($request);

                    $this->events->append('after');

                    return $response;
                }
            }
        );

        $response = $pipeline->handle(
            new Request('GET', '/health'),
            function (Request $request) use ($events): Response {
                $events->append('handler');

                return new Response(200, [], 'OK');
            }
        );

        self::assertSame(
            ['before', 'handler', 'after'],
            $events->getArrayCopy()
        );

        self::assertSame(200, $response->status);
        self::assertFalse($sessions->isActive());
    }

    public function testSessionClosesWhenHandlerThrows(): void
    {
        $sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $pipeline = new Pipeline();

        $pipeline->add(
            new SessionMiddleware($sessions)
        );

        $caught = null;

        try {
            $pipeline->handle(
                new Request('GET', '/failure'),
                function (Request $request) use ($sessions): Response {
                    self::assertTrue(
                        $sessions->isActive()
                    );

                    throw new RuntimeException(
                        'Simulated controller failure'
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
            'Simulated controller failure',
            $caught->getMessage()
        );

        self::assertFalse(
            $sessions->isActive()
        );
    }
}
