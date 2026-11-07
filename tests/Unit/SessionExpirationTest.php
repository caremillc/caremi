<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SessionExpirationTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testSessionExpiresAfterInactivity(): void
    {
        $now = 1000;

        $manager = new SessionManager(
            new SessionCookieConfig(),
            idleTimeout: 1800,
            clock: static fn (): int => $now,
        );

        $manager->start();
        $manager->set('task_filter', 'pending');
        $manager->close();

        $later = new SessionManager(
            new SessionCookieConfig(),
            idleTimeout: 1800,
            clock: static fn (): int => 2800,
        );

        $later->start();

        self::assertFalse(
            $later->has('task_filter')
        );
    }

    public function testSessionRemainsValidBeforeTimeout(): void
    {
        $manager = new SessionManager(
            new SessionCookieConfig(),
            idleTimeout: 1800,
            clock: static fn (): int => 1000,
        );

        $manager->start();
        $manager->set('task_filter', 'pending');
        $manager->close();

        $later = new SessionManager(
            new SessionCookieConfig(),
            idleTimeout: 1800,
            clock: static fn (): int => 1500,
        );

        $later->start();

        self::assertSame(
            'pending',
            $later->get('task_filter')
        );
    }

    public function testAuthenticationRotationClearsData(): void
    {
        $manager = new SessionManager(
            new SessionCookieConfig()
        );

        $manager->start();
        $manager->set('temporary', 'value');

        $oldId = session_id();

        $manager->rotateForAuthentication();

        self::assertNotSame(
            $oldId,
            session_id()
        );

        self::assertFalse(
            $manager->has('temporary')
        );

        $manager->set('user_id', 42);

        self::assertSame(
            42,
            $manager->get('user_id')
        );
    }

    public function testInvalidTimeoutIsRejected(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        new SessionManager(
            new SessionCookieConfig(),
            idleTimeout: 0,
        );
    }

    public function testReservedKeysCannotBeOverwritten(): void
    {
        $manager = new SessionManager(
            new SessionCookieConfig()
        );

        $manager->start();

        $this->expectException(
            \InvalidArgumentException::class
        );

        $manager->set(
            '__careminate_last_activity',
            0
        );
    }
}
