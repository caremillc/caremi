<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SessionManagerTest extends TestCase
{
    private SessionManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new SessionManager(
            new SessionCookieConfig()
        );
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testStartsSecureSession(): void
    {
        $this->manager->start();

        self::assertTrue($this->manager->isActive());

        self::assertSame(
            '1',
            ini_get('session.use_strict_mode')
        );

        self::assertSame(
            '1',
            ini_get('session.use_only_cookies')
        );

        $cookies = session_get_cookie_params();

        self::assertTrue($cookies['secure']);
        self::assertTrue($cookies['httponly']);
        self::assertSame('Lax', $cookies['samesite']);
    }

    public function testStoresAndRemovesValues(): void
    {
        $this->manager->start();

        $this->manager->set('task_filter', 'pending');

        self::assertSame(
            'pending',
            $this->manager->get('task_filter')
        );

        self::assertTrue(
            $this->manager->has('task_filter')
        );

        $this->manager->remove('task_filter');

        self::assertFalse(
            $this->manager->has('task_filter')
        );

        self::assertSame(
            'all',
            $this->manager->get('task_filter', 'all')
        );
    }

    public function testRejectsSecondStart(): void
    {
        $this->manager->start();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'A PHP session is already active.'
        );

        $this->manager->start();
    }

    public function testClosesSession(): void
    {
        $this->manager->start();

        $this->manager->set('example', 'value');

        $this->manager->close();

        self::assertFalse($this->manager->isActive());
    }

    public function testRejectsAccessWithoutStart(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'PHP session is not active.'
        );

        $this->manager->get('example');
    }


    public function testRejectsEmptySessionKey(): void
    {
        $this->manager->start();

        $this->expectException(
            \InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Invalid or reserved session key.'
        );

        $this->manager->set('', 'value');
    }

    public function testLogoutClearsAuthenticatedSession(): void
    {
        $this->manager->start();

        $this->manager->set('user_id', 42);
        $this->manager->set('task_filter', 'pending');

        $this->manager->logout();

        self::assertFalse($this->manager->isActive());
    }


    public function testLogoutRejectsOldSessionReplay(): void
    {
        $this->manager->start();

        $this->manager->rotateForAuthentication();
        $this->manager->set('user_id', 42);

        $oldId = session_id();

        self::assertIsString($oldId);
        self::assertNotSame('', $oldId);

        $this->manager->logout();

        // Simulate a later request supplying the old cookie ID.
        session_id($oldId);

        $this->manager->start();

        self::assertFalse(
            $this->manager->has('user_id')
        );

        self::assertNotSame(
            $oldId,
            session_id()
        );
    }


    public function testLogoutRequiresActiveSession(): void
    {
        $this->expectException(
            \RuntimeException::class
        );

        $this->expectExceptionMessage(
            'PHP session is not active.'
        );

        $this->manager->logout();
    }

    public function testLogoutPermitsFreshAnonymousSession(): void
    {
        $this->manager->start();

        $this->manager->set('user_id', 42);

        $this->manager->logout();

        $this->manager->start();

        self::assertFalse(
            $this->manager->has('user_id')
        );

        $this->manager->set('task_filter', 'all');

        self::assertSame(
            'all',
            $this->manager->get('task_filter')
        );
    }
}
