<?php  declare(strict_types=1);
define('CAREMI_START', microtime(true));
define('ROOT_PATH', __DIR__);
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__ . '/../public');

/**
 * This script demonstrates the use of memory limit settings and displays the current memory usage.
 * 
 * It includes the Composer autoloader, increases the memory limit to 1GB, and outputs the current
 * memory usage in MB.
 * 
 * Requirements:
 * - Composer autoloader (vendor/autoload.php) must be available.
 * - The memory_limit directive must be modifiable in your PHP environment.
 *
 * @package    caremi
 * @author     caremillc <caremillc@gmail.com>
 * @version    1.0.0
 * @since      2025-04-28
 */
require_once BASE_PATH . '/vendor/autoload.php';

// Increase memory limit before bootstrapping
ini_set('memory_limit', '1G');


// Output current memory usage in MB
echo sprintf('Memory usage: %.2f MB', memory_get_usage() / 1024 / 1024);