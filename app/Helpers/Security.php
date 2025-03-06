<?php

namespace App\Helpers;

class Security
{
    /**
     * Sanitize input data to prevent XSS attacks
     *
     * @param string $data Input data to sanitize
     * @return string Sanitized data
     */
    public static function sanitize($data)
    {
        if (is_string($data)) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = strip_tags($data);
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    /**
     * Sanitize an array of input data
     *
     * @param array $dataArray Array of input data
     * @return array Sanitized array
     */
    public static function sanitizeArray(array $dataArray)
    {
        $sanitized = [];
        foreach ($dataArray as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitize($value);
            }
        }
        return $sanitized;
    }

    /**
     * Validate and sanitize email addresses
     *
     * @param string $email Email to validate
     * @return string|false Sanitized email or false if invalid
     */
    public static function validateEmail($email)
    {
        $email = self::sanitize($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Generate secure hash for passwords
     *
     * @param string $password Raw password
     * @return string Hashed password
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID, ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 3]);
    }

    /**
     * Verify password against hash
     *
     * @param string $password Raw password
     * @param string $hash Stored hash
     * @return bool True if password matches hash
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate CSRF token and store in session
     *
     * @return string CSRF token
     */
    public static function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token against session token
     *
     * @param string $token Submitted token
     * @return bool True if token is valid
     */
    public static function verifyCsrfToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo "CSRF token verification failed.";
            exit;
        }
    }

    /**
     * Sanitize database inputs to prevent SQL injection
     *
     * @param string $input Input to sanitize
     * @param \PDO $pdo PDO connection instance
     * @return string Sanitized string
     */
    public static function sanitizeForDatabase($input, \PDO $pdo)
    {
        return $pdo->quote(self::sanitize($input));
    }

    /**
     * Set secure HTTP headers
     */
    public static function setSecureHeaders()
    {
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; connect-src 'self';");

        // Prevent MIME type sniffing
        header("X-Content-Type-Options: nosniff");

        // Clickjacking protection
        header("X-Frame-Options: DENY");

        // XSS protection (for older browsers)
        header("X-XSS-Protection: 1; mode=block");

        // Referrer Policy
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // HSTS (HTTP Strict Transport Security)
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }

    /**
     * Rate limit function to prevent brute force attacks
     *
     * @param string $key Unique identifier for the rate limit
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $period Time period in seconds
     * @return bool True if within rate limit, false if exceeded
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $period = 60)
    {
        $now = time();

        if (!isset($_SESSION['rate_limits'][$key])) {
            $_SESSION['rate_limits'][$key] = ['attempts' => 0, 'reset_at' => $now + $period];
        }

        if ($_SESSION['rate_limits'][$key]['reset_at'] <= $now) {
            $_SESSION['rate_limits'][$key] = ['attempts' => 0, 'reset_at' => $now + $period];
        }

        $_SESSION['rate_limits'][$key]['attempts']++;

        return $_SESSION['rate_limits'][$key]['attempts'] <= $maxAttempts;
    }
}
