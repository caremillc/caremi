<?php 

use Careminate\Core;
use Careminate\Routing\Router;

require_once BASE_PATH . '/vendor/autoload.php';
new Core;

// Load .env if needed
if (file_exists(BASE_PATH . '/.env')) {
    (Dotenv\Dotenv::createImmutable(BASE_PATH))->load();
}

  // In Application.php or a service provider
  if (getenv('APP_ENV') === 'production') {
    Router::setInternalRoutesEnabled(false); // Enable internal routes
}