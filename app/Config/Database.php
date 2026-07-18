<?php

namespace App\Config;

class Database
{
    public static function config(): array
    {
        return [
            'host'     => $_ENV['DB_HOST'],
            'port'     => $_ENV['DB_PORT'] ?? '5432',
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'database' => $_ENV['DB_NAME'],
            'pooler'   => [
                'host' => $_ENV['DB_POOLER_HOST'] ?? $_ENV['DB_HOST'],
                'port' => $_ENV['DB_POOLER_PORT'] ?? '6543',
            ],
        ];
    }
}