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
$posts = $schema->createTable("posts");

$posts->addColumn("id", Types::INTEGER, ["autoincrement" => true]);
$posts->addColumn("title", Types::STRING, ["length" => 255]);
$posts->addColumn("description", Types::STRING, ["length" => 255]);
$posts->addColumn("image", Types::BLOB);
$posts->addColumn("created_at", Types::DATETIME_MUTABLE,["notnull" => false]);
$posts->addColumn("updated_at", Types::DATETIME_MUTABLE,["notnull" => false]);
$posts->setPrimaryKey(["id"]);

// Generate and run SQL
$platform = $connection->getDatabasePlatform();
$queries = $schema->toSql($platform);

foreach ($queries as $query) {
    echo "Executing: $query\n";
    $connection->executeStatement($query);
}

echo "✅ posts table created successfully.\n";