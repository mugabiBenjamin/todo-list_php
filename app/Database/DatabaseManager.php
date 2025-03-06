<?php

namespace App\Database;

use App\Config\Database;
use PDO;

class DatabaseManager
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = Database::getConnection();
        }
        return self::$connection;
    }
}