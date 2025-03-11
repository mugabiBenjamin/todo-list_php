<?php

namespace App\Config;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? '',
    'database' => $_ENV['DB_NAME'] ?? 'todo_app',
];
