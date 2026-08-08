<?php

declare(strict_types=1);

use Careminate\Contracts\Exception\FrameworkException;
use Careminate\Support\Path\Path;
use Careminate\Version\FrameworkVersion;

require dirname(__DIR__) . '/vendor/autoload.php';

$version = FrameworkVersion::current();

echo $version->major().PHP_EOL;
echo FrameworkVersion::string().PHP_EOL;

try {
    $path = Path::from(
        'storage/../config'
    );
} catch (FrameworkException $exception) {
    echo $exception->getMessage();
}

// Path may only be used after Composer's autoloader is loaded.
// Keep your existing Path::... statement here.