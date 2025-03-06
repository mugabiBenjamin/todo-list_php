<?php

namespace App\Helpers;

class Security
{
    /**
     * Sanitizes user input to prevent XSS attacks.
     *
     * @param string $input The input to sanitize.
     * @return string The sanitized input.
     */
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input));
    }

    /**
     * Generates a CSRF token.
     *
     * @return string The CSRF token.
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verifies a CSRF token.
     *
     * @param string $token The token to verify.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function verifyCsrfToken(string $token): bool
    {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        if (hash_equals($_SESSION['csrf_token'], $token)) {
            unset($_SESSION['csrf_token']); // Invalidate token after use
            return true;
        }
        return false;
    }
}