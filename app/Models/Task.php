<?php

namespace App\Models;

class Task
{
    public function __construct(
        public readonly ?int $id,
        public string $name,
        public bool $completed = false,
    ) {}
}