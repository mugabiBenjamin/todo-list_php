<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Helpers\PasswordHasher;
use Exception;

class AuthService
{
    private const SESSION_USER_KEY = 'user_id';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasher $hasher
    ) {}

    public function login(string $email, string $password): bool
    {
        try {
            $user = $this->userRepository->findByEmail($email);

            if ($user === null) {
                return false;
            }

            if (!$this->hasher->verify($password, $user->password_hash)) {
                return false;
            }

            if ($this->hasher->needsRehash($user->password_hash)) {
                $user->password_hash = $this->hasher->hash($password);
                $this->userRepository->save($user);
            }

            session_regenerate_id(true);
            $_SESSION[self::SESSION_USER_KEY] = $user->id;

            return true;
        } catch (Exception $e) {
            error_log('Authentication error: ' . $e->getMessage());
            return false;
        }
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_KEY]);
        session_regenerate_id(true);
    }

    public function check(): bool
    {
        return isset($_SESSION[self::SESSION_USER_KEY]);
    }

    public function userId(): ?int
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }
}