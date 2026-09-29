<?php

namespace App\Models;

class User
{
    public function __construct(
        public readonly ?int $id,
        public string $email,
        public string $password_hash,
        public ?string $created_at = null,
    ) {}
}