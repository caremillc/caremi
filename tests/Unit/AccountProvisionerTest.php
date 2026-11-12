<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Security\AccountProvisioner;
use Careminate\Security\PasswordHasher;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AccountProvisionerTest extends TestCase
{
    public function testAccountStoresHashInsteadOfPlaintext(): void
    {
        $storedEmail = null;
        $storedHash = null;

        $provisioner = new AccountProvisioner(
            new PasswordHasher(),
            static function (
                string $email,
                string $hash,
            ) use (
                &$storedEmail,
                &$storedHash,
            ): string {
                $storedEmail = $email;
                $storedHash = $hash;

                return 'user-42';
            },
        );

        $password = 'Example-Secret-2026!';

        $id = $provisioner->provision(
            'person@example.test',
            $password,
        );

        self::assertSame('user-42', $id);
        self::assertSame(
            'person@example.test',
            $storedEmail,
        );
        self::assertIsString($storedHash);
        self::assertNotSame($password, $storedHash);

        self::assertTrue(
            new PasswordHasher()->verify(
                $password,
                $storedHash,
            ),
        );
    }

    public function testInvalidEmailNeverReachesRepository(): void
    {
        $calls = 0;

        $provisioner = new AccountProvisioner(
            new PasswordHasher(),
            static function (
                string $email,
                string $hash,
            ) use (&$calls): string {
                $calls++;

                return 'user-42';
            },
        );

        try {
            $provisioner->provision(
                'not-an-email',
                'Example-Secret-2026!',
            );

            self::fail('Invalid email was accepted.');
        } catch (InvalidArgumentException) {
            self::assertSame(0, $calls);
        }
    }

    public function testInvalidPasswordNeverReachesRepository(): void
    {
        $calls = 0;

        $provisioner = new AccountProvisioner(
            new PasswordHasher(),
            static function (
                string $email,
                string $hash,
            ) use (&$calls): string {
                $calls++;

                return 'user-42';
            },
        );

        try {
            $provisioner->provision(
                'person@example.test',
                str_repeat('a', 73),
            );

            self::fail('Invalid password was accepted.');
        } catch (InvalidArgumentException) {
            self::assertSame(0, $calls);
        }
    }

    public function testRepositoryFailurePropagates(): void
    {
        $provisioner = new AccountProvisioner(
            new PasswordHasher(),
            static function (
                string $email,
                string $hash,
            ): string {
                throw new RuntimeException(
                    'Database write failed.',
                );
            },
        );

        $this->expectException(
            RuntimeException::class,
        );

        $provisioner->provision(
            'person@example.test',
            'Example-Secret-2026!',
        );
    }

    public function testEmptyUserIdIsRejected(): void
    {
        $provisioner = new AccountProvisioner(
            new PasswordHasher(),
            static fn (
                string $email,
                string $hash,
            ): string => '',
        );

        $this->expectException(
            \UnexpectedValueException::class,
        );

        $provisioner->provision(
            'person@example.test',
            'Example-Secret-2026!',
        );
    }
}
