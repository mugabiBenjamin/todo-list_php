<?php

namespace App\Helpers;

class RateLimiter
{
    private const SESSION_KEY = 'rate_limits';

    public function check(string $key, int $maxAttempts = 5, int $period = 60): bool
    {
        $now = time();

        if (!isset($_SESSION[self::SESSION_KEY][$key])) {
            $_SESSION[self::SESSION_KEY][$key] = [
                'attempts' => 0,
                'reset_at' => $now + $period,
            ];
        }

        if ($_SESSION[self::SESSION_KEY][$key]['reset_at'] <= $now) {
            $_SESSION[self::SESSION_KEY][$key] = [
                'attempts' => 0,
                'reset_at' => $now + $period,
            ];
        }

        $_SESSION[self::SESSION_KEY][$key]['attempts']++;

        return $_SESSION[self::SESSION_KEY][$key]['attempts'] <= $maxAttempts;
    }

    public function reset(string $key): void
    {
        unset($_SESSION[self::SESSION_KEY][$key]);
    }
}