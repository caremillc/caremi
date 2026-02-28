<?php declare(strict_types=1);

define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', dirname(__FILE__));
define('ROOT_DIR', dirname(__FILE__));

require_once BASE_PATH . '/vendor/autoload.php';

return true;