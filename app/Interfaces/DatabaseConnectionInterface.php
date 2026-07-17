<?php

namespace App\Interfaces;

use PDO;

interface DatabaseConnectionInterface
{
    public function getConnection(): PDO;
}