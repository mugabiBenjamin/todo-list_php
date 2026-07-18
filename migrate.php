<?php

define('APP_ROOT', __DIR__);

if (php_sapi_name() !== 'cli') {
    echo 'This script must be run from the command line.' . PHP_EOL;
    exit(1);
}

require_once APP_ROOT . '/vendor/autoload.php';

use App\Config\Database;
use App\Database\DatabaseManager;
use App\Database\Migrations\CreateTasksTable;

$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

try {
    $db        = new DatabaseManager(Database::config());
    $migration = new CreateTasksTable($db);
    $migration->up();

    echo '[OK] Migration completed successfully.' . PHP_EOL;
    exit(0);

} catch (Throwable $e) {
    echo '[ERROR] Migration failed: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}