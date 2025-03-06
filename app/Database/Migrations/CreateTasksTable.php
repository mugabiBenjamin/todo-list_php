<?php

namespace App\Database\Migrations;

use App\Database\DatabaseManager;

class CreateTasksTable
{
    public static function up()
    {
        $db = DatabaseManager::getConnection();
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            completed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        echo "Tasks table created successfully.\n";
    }

    public static function down()
    {
        $db = DatabaseManager::getConnection();
        $sql = "DROP TABLE IF EXISTS tasks";
        $db->exec($sql);
        echo "Tasks table dropped successfully.\n";
    }
}