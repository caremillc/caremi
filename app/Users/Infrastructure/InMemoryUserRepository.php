<?php

declare(strict_types=1);

namespace App\Users\Infrastructure;

use App\Users\Contracts\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    public function exists(int $userId): bool
    {
        return $userId > 0;
    }
}