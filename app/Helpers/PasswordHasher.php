<?php

namespace App\Helpers;

use RuntimeException;

class PasswordHasher
{
    private const ALGORITHM = PASSWORD_ARGON2ID;

    private const OPTIONS = [
        'memory_cost' => 65536,
        'time_cost'   => 4,
        'threads'     => 3,
    ];

    public function hash(string $password): string
    {
        $hash = password_hash($password, self::ALGORITHM, self::OPTIONS);
        
        if ($hash === false) {
            throw new RuntimeException('Failed to generate Argon2id password hash.');
        }
        
        return $hash;
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, self::ALGORITHM, self::OPTIONS);
    }
}