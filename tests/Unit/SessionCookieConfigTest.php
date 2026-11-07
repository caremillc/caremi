<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Session\SessionCookieConfig;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SessionCookieConfigTest extends TestCase
{
    public function testSecureDefaults(): void
    {
        $config = new SessionCookieConfig();

        self::assertSame([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ], $config->toPhpOptions());
    }

    public function testExplicitLocalHttpConfiguration(): void
    {
        $config = new SessionCookieConfig(
            secure: false,
        );

        self::assertFalse(
            $config->toPhpOptions()['secure']
        );
    }

    public function testNegativeLifetimeRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SessionCookieConfig(lifetime: -1);
    }

    public function testInvalidPathRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SessionCookieConfig(path: 'relative/path');
    }

    public function testControlCharactersRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SessionCookieConfig(path: "/\r\n");
    }

    public function testInvalidSameSiteRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SessionCookieConfig(sameSite: 'None');
    }

    public function testHttpOnlyCannotBeDisabled(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SessionCookieConfig(httpOnly: false);
    }

    public function testStrictSameSiteSupported(): void
    {
        $config = new SessionCookieConfig(
            sameSite: 'Strict',
        );

        self::assertSame(
            'Strict',
            $config->toPhpOptions()['samesite']
        );
    }

    public function testInvalidDomainRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SessionCookieConfig(
            domain: "example.com\r\n",
        );
    }
}
