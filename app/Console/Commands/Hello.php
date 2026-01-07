<?php declare(strict_types=1);
namespace App\Console\Commands;

use Careminate\Console\Commands\Command;

class Hello extends Command
{
    protected string $signature = 'Hello';
    protected string $description = 'Describe your command';

    public function handle(array $arguments = []): int
    {
        echo "Hello executed!\n";
        return 0;
    }
}