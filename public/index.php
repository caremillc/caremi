<?php declare(strict_types=1);

use Careminate\Container\ServiceContainer;
use Careminate\Databases\Contracts\DatabaseConnectionInterface;
define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);
define('ROOT_DIR', __DIR__ . '/../public');
define('PUBLIC_PATH', __DIR__);


require_once BASE_PATH . '/bootstrap/app.php';

$app = (new Careminate\Application())->start();
$app->register(Careminate\ServiceProviders\DatabaseServiceProvider::class);

$db = ServiceContainer::resolve(DatabaseConnectionInterface::class);
$pdo = $db->getPDO();


try {
    // Bootstrap the app
    // Create $app instance BEFORE you try to use it

} catch (\Throwable $e) {
    http_response_code(500);
    echo "Application error: " . $e->getMessage();
} 
