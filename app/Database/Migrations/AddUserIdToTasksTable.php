<?php

namespace App\Database\Migrations;

use App\Interfaces\DatabaseConnectionInterface;
use PDOException;
use RuntimeException;

class AddUserIdToTasksTable
{
    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function up(): void
    {
        $sql = "ALTER TABLE tasks 
                ADD COLUMN IF NOT EXISTS user_id INTEGER,
                ADD CONSTRAINT fk_tasks_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";

        try {
            $this->db->getConnection()->exec($sql);
        } catch (PDOException $e) {
            error_log('Failed to alter tasks table: ' . $e->getMessage());
            throw new RuntimeException('Database migration failed during tasks table alteration.');
        }
    }
}