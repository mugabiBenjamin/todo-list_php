<?php

namespace App\Validators;

class AuthValidator
{
    private const MIN_PASSWORD_LENGTH = 8;
    private array $errors = [];

    public function validateLogin(array $data): bool
    {
        $this->errors = [];
        $this->validateEmail($data['email'] ?? '');
        $this->validatePasswordPresent($data['password'] ?? '');
        return empty($this->errors);
    }

    public function validateRegistration(array $data): bool
    {
        $this->errors = [];
        $this->validateEmail($data['email'] ?? '');
        $this->validatePasswordStrength($data['password'] ?? '');
        $this->validatePasswordMatch($data['password'] ?? '', $data['password_confirmation'] ?? '');
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return $this->errors[0] ?? '';
    }

    private function validateEmail(string $email): void
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Please provide a valid email address.';
        }
    }

    private function validatePasswordPresent(string $password): void
    {
        if (empty($password)) {
            $this->errors[] = 'Password is required.';
        }
    }

    private function validatePasswordStrength(string $password): void
    {
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $this->errors[] = sprintf('Password must be at least %d characters long.', self::MIN_PASSWORD_LENGTH);
        }
    }

    private function validatePasswordMatch(string $password, string $passwordConfirmation): void
    {
        if ($password !== $passwordConfirmation) {
            $this->errors[] = 'Passwords do not match.';
        }
    }
}