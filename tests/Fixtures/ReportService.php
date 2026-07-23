<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

final readonly class ReportService
{
    public function __construct(public Writer $writer)
    {
    }
}