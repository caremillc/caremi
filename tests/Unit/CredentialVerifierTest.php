<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Security\CredentialVerifier;
use Careminate\Security\PasswordHasher;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CredentialVerifierTest extends TestCase
{
    public function testCorrectCredentialsAreAccepted(): void
    {
        $hasher = new PasswordHasher();

        $verifier = new CredentialVerifier(
            $hasher,
            $hasher->hash('unused-dummy-credential'),
        );

        $storedHash = $hasher->hash(
            'Correct-Secret-2026!',
        );

        self::assertTrue(
            $verifier->verify(
                'Correct-Secret-2026!',
                $storedHash,
            ),
        );
    }

    public function testIncorrectCredentialsAreRejected(): void
    {
        $hasher = new PasswordHasher();

        $verifier = new CredentialVerifier(
            $hasher,
            $hasher->hash('unused-dummy-credential'),
        );

        $storedHash = $hasher->hash(
            'Correct-Secret-2026!',
        );

        self::assertFalse(
            $verifier->verify(
                'Incorrect-Secret',
                $storedHash,
            ),
        );
    }

    public function testNonexistentAccountIsRejected(): void
    {
        $hasher = new PasswordHasher();

        $verifier = new CredentialVerifier(
            $hasher,
            $hasher->hash('unused-dummy-credential'),
        );

        self::assertFalse(
            $verifier->verify(
                'Correct-Secret-2026!',
                null,
            ),
        );
    }

    public function testDummyPasswordCannotAuthenticate(): void
    {
        $hasher = new PasswordHasher();

        $dummyPassword = 'unused-dummy-credential';

        $verifier = new CredentialVerifier(
            $hasher,
            $hasher->hash($dummyPassword),
        );

        self::assertFalse(
            $verifier->verify(
                $dummyPassword,
                null,
            ),
        );
    }

    public function testInvalidDummyHashIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class,
        );

        new CredentialVerifier(
            new PasswordHasher(),
            'invalid-hash',
        );
    }

    public function testMalformedStoredHashIsRejected(): void
    {
        $hasher = new PasswordHasher();

        $verifier = new CredentialVerifier(
            $hasher,
            $hasher->hash('unused-dummy-credential'),
        );

        self::assertFalse(
            $verifier->verify(
                'Example-Secret',
                'invalid-stored-hash',
            ),
        );
    }
}
