<?php

namespace App\Database\Migrations;

use App\Interfaces\DatabaseConnectionInterface;
use PDOException;
use RuntimeException;

class CreateUsersTable
{
    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $this->db->getConnection()->exec($sql);
        } catch (PDOException $e) {
            error_log('Failed to create users table: ' . $e->getMessage());
            throw new RuntimeException('Database migration failed during users table creation.');
        }
    }
}