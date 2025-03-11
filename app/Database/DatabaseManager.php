<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseManager
{
    private $connection;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']}";
            $this->connection = new PDO($dsn, $this->config['user'], $this->config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new \Exception("Database connection failed.");
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function migrate()
    {
        require_once __DIR__ . '/Migrations/CreateTasksTable.php';
        Migrations\CreateTasksTable::up($this);
    }
}
