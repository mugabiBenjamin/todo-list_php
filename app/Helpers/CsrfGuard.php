<?php

namespace App\Helpers;

class CsrfGuard
{
    private const TOKEN_KEY = 'csrf_token';
    private const TOKEN_BYTES = 32;

    public function generateToken(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(self::TOKEN_BYTES));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public function verifyToken(string $token): void
    {
        if (
            empty($_SESSION[self::TOKEN_KEY]) ||
            empty($token) ||
            !hash_equals($_SESSION[self::TOKEN_KEY], $token)
        ) {
            http_response_code(403);
            echo 'CSRF token verification failed.';
            exit;
        }
    }
}