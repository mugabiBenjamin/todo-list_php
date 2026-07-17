<?php

namespace App\Validators;

class TaskValidator
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 255;

    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];
        $this->validateName($data['name'] ?? '');
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

    private function validateName(string $name): void
    {
        $length = strlen($name);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            $this->errors[] = sprintf(
                'Task name must be between %d and %d characters.',
                self::MIN_LENGTH,
                self::MAX_LENGTH
            );
        }
    }
}