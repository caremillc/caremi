<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Security\PasswordHasher;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PasswordHasherTest extends TestCase
{
    public function testPasswordCanBeHashedAndVerified(): void
    {
        $hasher = new PasswordHasher();

        $password = 'Example-Secret-2026!';
        $hash = $hasher->hash($password);

        self::assertNotSame($password, $hash);
        self::assertTrue(
            $hasher->verify($password, $hash),
        );
        self::assertFalse(
            $hasher->verify('incorrect', $hash),
        );
    }

    public function testRepeatedHashesUseDifferentSalts(): void
    {
        $hasher = new PasswordHasher();

        $first = $hasher->hash('Example-Secret-2026!');
        $second = $hasher->hash('Example-Secret-2026!');

        self::assertNotSame($first, $second);

        self::assertTrue(
            $hasher->verify('Example-Secret-2026!', $first),
        );

        self::assertTrue(
            $hasher->verify('Example-Secret-2026!', $second),
        );
    }

    public function testEmptyPasswordIsRejected(): void
    {
        $hasher = new PasswordHasher();

        $this->expectException(
            InvalidArgumentException::class,
        );

        $hasher->hash('');
    }

    public function testPasswordLongerThan72BytesIsRejected(): void
    {
        $hasher = new PasswordHasher();

        $this->expectException(
            InvalidArgumentException::class,
        );

        $hasher->hash(str_repeat('a', 73));
    }

    public function testNullByteIsRejected(): void
    {
        $hasher = new PasswordHasher();

        $this->expectException(
            InvalidArgumentException::class,
        );

        $hasher->hash("example\0secret");
    }

    public function testLongPasswordCannotBypassBcryptLimit(): void
    {
        $hasher = new PasswordHasher();

        $hash = $hasher->hash(str_repeat('a', 72));

        self::assertFalse(
            $hasher->verify(str_repeat('a', 73), $hash),
        );
    }

    public function testCurrentHashDoesNotNeedRehash(): void
    {
        $hasher = new PasswordHasher();

        $hash = $hasher->hash('Example-Secret-2026!');

        self::assertFalse(
            $hasher->needsRehash($hash),
        );
    }

    public function testOlderBcryptCostRequiresRehash(): void
    {
        $hasher = new PasswordHasher();

        $oldHash = password_hash(
            'Example-Secret-2026!',
            PASSWORD_BCRYPT,
            ['cost' => 10],
        );

        self::assertTrue(
            $hasher->verify('Example-Secret-2026!', $oldHash),
        );

        self::assertTrue(
            $hasher->needsRehash($oldHash),
        );
    }
}
