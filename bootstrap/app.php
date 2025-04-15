<?php 

use Careminate\Core;
use Careminate\Routing\Router;

require_once BASE_PATH . '/vendor/autoload.php';

// Optionally: new Core; — keep if needed
new Core;

// Load .env if needed
if (file_exists(BASE_PATH . '/.env')) {
    (Dotenv\Dotenv::createImmutable(BASE_PATH))->load();
}

// Enable/disable internal routes based on env
if (getenv('APP_ENV') === 'production') {
    Router::setInternalRoutesEnabled(false);
}


