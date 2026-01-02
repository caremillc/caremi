# Careminate Database Connection Guide

This guide explains how Careminate handles environment configuration, database setup, and automatic Doctrine DBAL connection management — with inline comment stripping in `.env` values.

---

## 🧩 Step 1: Environment File (`.env`)

Your `.env` file defines key environment variables. Comments (`# ...`) are supported and safely ignored.

Example:

```env
APP_NAME=Caremi
APP_ENV=development
APP_VERSION=1.0.0
APP_DEBUG=true

# Database Configuration
DB_CONNECTION=sqlite  # Use sqlite, mysql, or pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dev_caremi
DB_SQLITE=storage/database.sqlite
DB_USERNAME=root
DB_PASSWORD=

# Optional database URL (overrides DB_CONNECTION if set)
# DATABASE_URL=mysql://root:@127.0.0.1:3306/dev_caremi
```

---

## ⚙️ Step 2: Configuration File (`config/database.php`)

This file defines available database connections and their parameters.

```php
<?php declare(strict_types=1);

return [
    'default' => env('DB_CONNECTION', 'sqlite'),

    'connections' => [
        'sqlite' => [
            'driver' => 'pdo_sqlite',
            'url' => null,
            'path' => BASE_PATH . '/' . env('DB_SQLITE', 'storage/database.sqlite'),
        ],

        'mysql' => [
            'driver'   => 'pdo_mysql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'dbname'   => env('DB_DATABASE', 'careminate'),
            'user'     => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8mb4',
        ],

        'pgsql' => [
            'driver'   => 'pdo_pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '5432'),
            'dbname'   => env('DB_DATABASE', 'careminate'),
            'user'     => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
        ],
    ],
];

?>

```

---

## 🧠 Step 3: Connection Factory (`Careminate/Database/Connections/Factory/ConnectionFactory.php`)

This factory creates Doctrine DBAL connections using either `DATABASE_URL` or the configured connection array.

```php
<?php declare(strict_types=1);

namespace Careminate\Database\Connections\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    protected array $dbConfig;

    public function __construct(array $dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    public function create(?string $name = null): Connection
    {
        $databaseUrl = env('DATABASE_URL', null);
        if (!empty($databaseUrl)) {
            return DriverManager::getConnection(['url' => $databaseUrl]);
        }

        $name = $name ?: $this->dbConfig['default'];
        $connectionConfig = $this->dbConfig['connections'][$name] ?? null;

        if (!$connectionConfig) {
            throw new \InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        if (!empty($connectionConfig['url'])) {
            return DriverManager::getConnection(['url' => $connectionConfig['url']]);
        }

        return DriverManager::getConnection($connectionConfig);
    }
}
?>

```

---

## 🧱 Step 4: Container Binding (`config/container.php`)

Registers the `ConnectionFactory` and shared `Doctrine\DBAL\Connection` in the service container.

```php
<?php
$dbConfig = require BASE_PATH . '/config/database.php';

$container->add(\Careminate\Database\Connections\Factory\ConnectionFactory::class)
    ->addArguments([
        new \League\Container\Argument\Literal\ArrayArgument($dbConfig)
    ]);

$container->addShared(\Doctrine\DBAL\Connection::class, function () use ($container): \Doctrine\DBAL\Connection {
    return $container->get(\Careminate\Database\Connections\Factory\ConnectionFactory::class)->create();
});
?>
```

---

## ⚡ Step 5: Environment Helper (`Careminate/Support/Helpers/functions.php`)

Cleans up environment variables and strips inline comments automatically.

```php
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        static $dotenv = null;

        if ($dotenv === null) {
            $dotenv = true;
        }

        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        if (is_string($value)) {
            $value = preg_replace('/\s+#.*/', '', $value);
            $value = trim($value, " \t\n\r\0\x0B\"'");
        }

        switch (strtolower($value)) {
            case 'true':  return true;
            case 'false': return false;
            case 'null':  return null;
        }

        return $value;
    }
}
```

---

## 🧪 Step 6: Kernel Test (`kernl.php`)

You can test the database connection by resolving it from the container:

```php
<?php
dd($this->container->get(Connection::class));
?>
```
This will dump the configured Doctrine DBAL connection object.

---

## 🧭 Use Cases

### 1. Connect to SQLite (default)
If `.env` has `DB_CONNECTION=sqlite`, Careminate uses `storage/database.sqlite` automatically.

### 2. Switch to MySQL
Update `.env`:
```env
DB_CONNECTION=mysql
DB_DATABASE=dev_caremi
DB_USERNAME=root
DB_PASSWORD=secret
```
Then your connection instantly points to MySQL.

### 3. Use DATABASE_URL
Instead of configuring separate DB keys, you can provide:
```env
DATABASE_URL=mysql://root:secret@127.0.0.1:3306/dev_caremi
```
The system prioritizes `DATABASE_URL` automatically.

---

## ✅ Benefits

- 💡 Inline comments supported in `.env`
- 🧹 Automatic trimming and type conversion
- ⚡ Centralized container-based connection
- 🔄 Doctrine DBAL flexibility for any supported driver
- 🧩 Easily switch between SQLite, MySQL, or PostgreSQL

---

© 2025 Careminate Framework — Database Layer Documentation