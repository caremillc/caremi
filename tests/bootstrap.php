<?php

declare(strict_types=1);

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException(
        'Composer autoloader was not found. Run "composer install" from the project root.'
    );
}

require $autoload;