<?php

namespace App\Helpers;

class InputSanitizer
{
    public function sanitize(string $data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_array($value)
                ? $this->sanitizeArray($value)
                : $this->sanitize($value);
        }
        return $sanitized;
    }

    public function validateEmail(string $email): string|false
    {
        return filter_var($this->sanitize($email), FILTER_VALIDATE_EMAIL);
    }
}