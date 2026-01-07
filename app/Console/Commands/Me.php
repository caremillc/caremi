<?php declare(strict_types=1);

namespace App\Console\Commands;

use Careminate\Console\Commands\Command;

class Me extends Command
{
    protected string $signature = 'me';
    protected string $description = 'Describe your command';

    public function handle(array $arguments = []): int
    {
        echo "Me executed!\n";
        return 0;
    }
}