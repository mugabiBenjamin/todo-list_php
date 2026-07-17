<?php

namespace App\Database\Migrations;

use App\Interfaces\DatabaseConnectionInterface;

class CreateTasksTable
{
    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id      SERIAL PRIMARY KEY,
            name    VARCHAR(255) NOT NULL,
            completed BOOLEAN DEFAULT FALSE
        )";

        $this->db->getConnection()->exec($sql);
    }
}