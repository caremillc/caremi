<?php declare(strict_types=1);

use Careminate\Application;
use Careminate\Encryption\Encrypter;

// Define BASE_PATH if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Autoload Composer
require BASE_PATH . '/vendor/autoload.php';

// Create application instance
$app = new Application();

// Load all configured service providers (including Environment)
// $config = [
//     'providers' => require BASE_PATH . '/config/providers.php',
// ];


// Load providers (deferred + eager)
$app->loadProviders(require BASE_PATH . '/config/providers.php');



// // Register each provider
// foreach ($config['providers'] as $providerClass) {
//     (new $providerClass())->register();
// }

// Now use env() because environment is loaded
// $appKey = env('APP_KEY');
// $encrypter = new Encrypter($appKey);
// $GLOBALS['encrypter'] = $encrypter;
// Example: Use a bound service
// $encrypter = $app->make(\Careminate\Encryption\Encrypter::class);
// $GLOBALS['encrypter'] = $encrypter;

$encrypter = $app->make(\Careminate\Encryption\Encrypter::class);
$GLOBALS['encrypter'] = $encrypter;

load_env();
