<?php

declare(strict_types=1);

namespace Careminate\Tests\Fixtures;

use Careminate\Tests\Fixtures\Writer;

final readonly class ContextualReportService
{
    public function __construct(public Writer $writer)
    {
    }
}