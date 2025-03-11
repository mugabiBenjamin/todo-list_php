<?php

namespace App\Database\Migrations;

use App\Database\DatabaseManager;

class CreateTasksTable
{
    public static function up(DatabaseManager $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            completed TINYINT(1) DEFAULT 0
        )";
        $db->getConnection()->exec($sql);
    }
}
