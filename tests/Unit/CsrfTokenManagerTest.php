<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Security\CsrfTokenManager;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CsrfTokenManagerTest extends TestCase
{
    private SessionManager $sessions;
    private CsrfTokenManager $csrf;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sessions = new SessionManager(
            new SessionCookieConfig()
        );

        $this->sessions->start();

        $this->csrf = new CsrfTokenManager(
            $this->sessions
        );
    }

    protected function tearDown(): void
    {
        if ($this->sessions->isActive()) {
            $this->sessions->close();
        }

        parent::tearDown();
    }

    public function testGeneratesValidToken(): void
    {
        $token = $this->csrf->token();

        self::assertMatchesRegularExpression(
            '/\A[a-f0-9]{64}\z/D',
            $token
        );

        self::assertTrue(
            $this->csrf->verify($token)
        );
    }

    public function testTokenRemainsStable(): void
    {
        $first = $this->csrf->token();
        $second = $this->csrf->token();

        self::assertSame($first, $second);
    }

    public function testRejectsInvalidToken(): void
    {
        $valid = $this->csrf->token();

        $invalid = $valid[0] === 'a'
            ? 'b' . substr($valid, 1)
            : 'a' . substr($valid, 1);

        self::assertFalse(
            $this->csrf->verify($invalid)
        );

        self::assertFalse(
            $this->csrf->verify('invalid')
        );

        self::assertFalse(
            $this->csrf->verify('')
        );

        self::assertFalse(
            $this->csrf->verify(null)
        );

        self::assertFalse(
            $this->csrf->verify(['token'])
        );
    }

    public function testMissingStoredTokenIsRejected(): void
    {
        self::assertFalse(
            $this->csrf->verify(str_repeat('a', 64))
        );
    }

    public function testRegenerationInvalidatesOldToken(): void
    {
        $old = $this->csrf->token();

        $new = $this->csrf->regenerate();

        self::assertNotSame($old, $new);

        self::assertFalse(
            $this->csrf->verify($old)
        );

        self::assertTrue(
            $this->csrf->verify($new)
        );
    }

    public function testMalformedStoredTokenIsReplaced(): void
    {
        $this->sessions->set(
            'csrf_token',
            'malformed'
        );

        $new = $this->csrf->token();

        self::assertMatchesRegularExpression(
            '/\A[a-f0-9]{64}\z/D',
            $new
        );

        self::assertTrue(
            $this->csrf->verify($new)
        );
    }

    public function testRequiresActiveSession(): void
    {
        $this->sessions->close();

        $this->expectException(
            RuntimeException::class
        );

        $this->csrf->token();
    }
}
