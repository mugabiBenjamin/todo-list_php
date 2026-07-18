<?php

namespace App\Database;

use PDO;
use PDOException;
use App\Interfaces\DatabaseConnectionInterface;

class DatabaseManager implements DatabaseConnectionInterface
{
    private PDO $connection;

    public function __construct(private readonly array $config)
    {
        $this->connection = $this->connect();
    }

    private function connect(): PDO
    {
        try {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
                $this->config['host'],
                $this->config['port'],
                $this->config['database']
            );

            $pdo = new PDO($dsn, $this->config['user'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
            ]);

            return $pdo;

        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Database connection failed.');
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}