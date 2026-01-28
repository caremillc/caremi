<?php declare(strict_types=1);

use App\Models\User;
use Careminate\Database\BaseModel;
use Careminate\Database\Drivers\MySQLConnection;
use Careminate\Database\Drivers\SQLiteConnection;
// ---------------------------------------------------------
// Define application constants
// ---------------------------------------------------------
define('CAREMI_START', microtime(true));      // Application start time
define('BASE_PATH', dirname(__DIR__));        // Project base directory
define('BOOTSTRAP_PATH', __DIR__);            // Bootstrap directory
define('CONFIG_PATH', BASE_PATH . '/config'); // Config directory
define('PUBLIC_PATH', BASE_PATH . '/public'); // Public directory
define('STORAGE_PATH', BASE_PATH . '/storage'); // storage directory
define('FRAMEWORK_PATH', BASE_PATH . '/framework');// framework directory

// Ensure storage directory exists
if (!is_dir(STORAGE_PATH)) {
    mkdir(STORAGE_PATH, 0755, true);
    mkdir(STORAGE_PATH . '/database', 0755, true);
    mkdir(STORAGE_PATH . '/cache', 0755, true);
    mkdir(STORAGE_PATH . '/logs', 0755, true);
    mkdir(STORAGE_PATH . '/sessions', 0755, true);
}


/**
 * Bootstrap the application
 */

// Define base path
$basePath = dirname(__DIR__);

// ---------------------------------------------------------
// Load Composer's autoloader (if available)
// ---------------------------------------------------------
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    die('Autoloader not found. Please run "composer install".');
}

// ---------------------------------------------------------
// Load performance utilities (logs execution time, etc.)
// ---------------------------------------------------------
require_once BOOTSTRAP_PATH . '/performance.php';
require_once BOOTSTRAP_PATH . '/env.php';

// ---------------------------------------------------------
// Set error handling
// ---------------------------------------------------------
if (!function_exists('app_debug_mode')) {
    function app_debug_mode(): bool
    {
        return filter_var(env('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOL);
    }
}

if (app_debug_mode()) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// start db connection
// Resolve driver from config
$driver = config('database.default', 'sqlite');

$connection = match ($driver) {
    'mysql'  => new MySQLConnection(),
    'sqlite' => new SQLiteConnection(),
    default  => throw new RuntimeException("Unsupported DB driver: {$driver}")
};

// 🔥 THIS IS THE MISSING LINE
BaseModel::setConnection($connection->getPDO());
// end db connection

//start db test cases
//$users = User::all();
// dd($users);
//$user = User::find(2);
// dd($user);
// $newuser = User::create([
//     'name' => 'Eric',
//     'email' => 'eric@test.com',
//     'password' => password_hash('secret', PASSWORD_BCRYPT),
// ]);
// dd($newuser);
//end db test cases   

// Create the application instance
$app = new \Careminate\Foundation\Application($basePath);

// dd($app);
// Return the application instance
return $app;