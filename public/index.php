<?php declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_START', microtime(true));

require_once BASE_PATH . '/vendor/autoload.php';

echo "Hello World";