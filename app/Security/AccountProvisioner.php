<?php

declare(strict_types=1);

namespace App\Security;

use Careminate\Security\PasswordHasher;
use Closure;
use InvalidArgumentException;
use SensitiveParameter;
use UnexpectedValueException;

final readonly class AccountProvisioner
{
    /**
     * The callback stores a new user with a password hash.
     *
     * @param Closure(string, string): string $createUser
     *        Arguments: email, password hash.
     *        Return value: created user ID.
     */
    public function __construct(
        private PasswordHasher $hasher,
        private Closure $createUser,
    ) {
    }

    public function provision(
        string $email,
        #[SensitiveParameter] string $password,
    ): string {
        $email = trim($email);

        if (
            strlen($email) > 254
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            throw new InvalidArgumentException(
                'Invalid account email address.',
            );
        }

        // Invalid passwords fail before any database write.
        $hash = $this->hasher->hash($password);

        // Only the hash crosses the persistence boundary.
        $userId = ($this->createUser)(
            $email,
            $hash,
        );

        if ($userId === '') {
            throw new UnexpectedValueException(
                'Account repository returned an empty user ID.',
            );
        }

        return $userId;
    }
}
