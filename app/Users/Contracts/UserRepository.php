<?php

declare(strict_types=1);

namespace App\Users\Contracts;

interface UserRepository
{
    public function exists(int $userId): bool;
}