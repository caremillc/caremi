<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Security\CredentialVerifier;
use Careminate\Security\PasswordHasher;
use Careminate\Security\SessionLoginService;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use LogicException;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SessionLoginServiceTest extends TestCase
{
    private SessionManager $sessions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sessions = new SessionManager(
            new SessionCookieConfig(),
        );

        $this->sessions->start();
    }

    protected function tearDown(): void
    {
        if ($this->sessions->isActive()) {
            $this->sessions->close();
        }

        parent::tearDown();
    }

    public function testSuccessfulLoginRotatesSession(): void
    {
        $service = $this->service();

        $previousId = session_id();

        self::assertTrue(
            $service->login(
                'user@example.test',
                'Correct-Secret-2026!',
            )
        );

        self::assertNotSame(
            $previousId,
            session_id(),
        );

        self::assertSame(
            'user-42',
            $service->currentUserId(),
        );
    }

    public function testWrongPasswordDoesNotAuthenticate(): void
    {
        $service = $this->service();

        self::assertFalse(
            $service->login(
                'user@example.test',
                'Incorrect-Secret',
            )
        );

        self::assertNull(
            $service->currentUserId(),
        );
    }

    public function testUnknownAccountDoesNotAuthenticate(): void
    {
        $service = $this->service();

        self::assertFalse(
            $service->login(
                'missing@example.test',
                'Correct-Secret-2026!',
            )
        );

        self::assertNull(
            $service->currentUserId(),
        );
    }

    public function testAlreadyAuthenticatedSessionCannotSwitchUser(): void
    {
        $service = $this->service();

        self::assertTrue(
            $service->login(
                'user@example.test',
                'Correct-Secret-2026!',
            )
        );

        $this->expectException(
            LogicException::class,
        );

        $service->login(
            'user@example.test',
            'Correct-Secret-2026!',
        );
    }

    public function testLogoutClearsAuthenticatedSession(): void
    {
        $service = $this->service();

        self::assertTrue(
            $service->login(
                'user@example.test',
                'Correct-Secret-2026!',
            )
        );

        $service->logout();

        self::assertFalse(
            $this->sessions->isActive(),
        );
    }

    private function service(): SessionLoginService
    {
        $hasher = new PasswordHasher();

        $storedHash = $hasher->hash(
            'Correct-Secret-2026!',
        );

        $verifier = new CredentialVerifier(
            $hasher,
            $hasher->hash('unused-dummy-secret'),
        );

        return new SessionLoginService(
            $this->sessions,
            $verifier,
            static function (string $email) use (
                $storedHash
            ): ?array {
                if ($email !== 'user@example.test') {
                    return null;
                }

                return [
                    'id' => 'user-42',
                    'password_hash' => $storedHash,
                ];
            },
        );
    }
}
