<?php

declare(strict_types=1);

namespace App\Services;

final readonly class ApplicationIdentity
{
    public function __construct(
        public string $name,
        public string $environment,
    ) {
    }
}
