<?php declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

// Define base path constant
define('BASE_PATH', realpath(__DIR__ . '/../../'));

// Load container
$container = require BASE_PATH . '/config/container.php';

// Get the shared Doctrine DBAL connection from container
/** @var \Doctrine\DBAL\Connection $connection */
$connection = $container->get(\Doctrine\DBAL\Connection::class);

// Build schema using DBAL Schema API
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

$schema = new Schema();
$users = $schema->createTable("users");

$users->addColumn("id", Types::INTEGER, ["autoincrement" => true]);
$users->addColumn("username", Types::STRING, ["length" => 255]);
$users->addColumn("email", Types::STRING, ["length" => 255]);
$users->addColumn("role", Types::STRING, ["length" => 255, "notnull" => false]); // nullable
$users->addColumn("password", Types::STRING, ["length" => 255]);
$users->addColumn("created_at", Types::DATETIME_MUTABLE,["notnull" => false]);
$users->addColumn("updated_at", Types::DATETIME_MUTABLE,["notnull" => false]);

$users->setPrimaryKey(["id"]);
$users->addUniqueIndex(["email"]);

// Generate and run SQL
$platform = $connection->getDatabasePlatform();
$queries = $schema->toSql($platform);

foreach ($queries as $query) {
    echo "Executing: $query\n";
    $connection->executeStatement($query);
}

echo "✅ users table created successfully.\n";



