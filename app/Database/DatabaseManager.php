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
            $usePooler = !empty($this->config['pooler']['host']);

            $host = $usePooler
                ? $this->config['pooler']['host']
                : $this->config['host'];

            $port = $usePooler
                ? $this->config['pooler']['port']
                : $this->config['port'];

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
                $host,
                $port,
                $this->config['database']
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => $usePooler,
                PDO::ATTR_PERSISTENT         => true,
            ];

            return new PDO($dsn, $this->config['user'], $this->config['password'], $options);

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