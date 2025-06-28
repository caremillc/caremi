<?php declare(strict_types=1);

use Dotenv\Dotenv;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require BASE_PATH . '/vendor/autoload.php';

// Load environment variables and validate required ones
$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// Optional: validate required .env variables
$requiredKeys = ['APP_NAME', 'APP_ENV', 'APP_KEY'];
foreach ($requiredKeys as $key) {
    if (!isset($_ENV[$key]) || trim($_ENV[$key]) === '') {
        throw new RuntimeException("Missing required environment key: $key");
    }
}
