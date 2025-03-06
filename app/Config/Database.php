<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    public static function getConnection()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}