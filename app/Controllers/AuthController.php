<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Interfaces\UserRepositoryInterface;
use App\Helpers\PasswordHasher;
use App\Helpers\CsrfGuard;
use App\Helpers\InputSanitizer;
use App\Helpers\RateLimiter;
use App\Validators\AuthValidator;
use App\Models\User;
use App\Config\Paths;
use Exception;

class AuthController
{
    public function __construct(
        private readonly AuthService             $authService,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasher          $hasher,
        private readonly CsrfGuard               $csrf,
        private readonly InputSanitizer          $sanitizer,
        private readonly RateLimiter             $rateLimiter,
        private readonly AuthValidator           $validator,
    ) {}

    public function showLogin(): void
    {
        if ($this->authService->check()) {
            header('Location: /');
            exit;
        }
        require Paths::views() . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'login.php';
    }

    public function login(array $data): void
    {
        if (!$this->rateLimiter->check('login_attempt', 5, 300)) {
            http_response_code(429);
            echo 'Too many login attempts. Please try again later.';
            return;
        }

        $this->csrf->verifyToken($data['csrf_token'] ?? '');

        $email    = $this->sanitizer->validateEmail($data['email'] ?? '');
        $password = $data['password'] ?? ''; 

        if (!$email || !$this->validator->validateLogin(['email' => $email, 'password' => $password])) {
            http_response_code(400);
            echo $this->validator->firstError();
            return;
        }

        if ($this->authService->login($email, $password)) {
            $this->rateLimiter->reset('login_attempt');
            header('Location: /');
            exit;
        }

        http_response_code(401);
        echo 'Invalid credentials.';
    }

    public function showRegister(): void
    {
        if ($this->authService->check()) {
            header('Location: /');
            exit;
        }
        require Paths::views() . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'register.php';
    }

    public function register(array $data): void
    {
        if (!$this->rateLimiter->check('register_attempt', 3, 3600)) {
            http_response_code(429);
            echo 'Too many registration attempts. Please try again later.';
            return;
        }

        $this->csrf->verifyToken($data['csrf_token'] ?? '');

        $email    = $this->sanitizer->validateEmail($data['email'] ?? '');
        $password = $data['password'] ?? ''; 

        if (!$email || !$this->validator->validateRegistration(['email' => $email, 'password' => $password])) {
            http_response_code(400);
            echo $this->validator->firstError();
            return;
        }

        try {
            if ($this->userRepository->findByEmail($email) !== null) {
                http_response_code(409);
                echo 'Email is already in use.';
                return;
            }

            $hashedPassword = $this->hasher->hash($password);
            $user = new User(null, $email, $hashedPassword);
            
            if ($this->userRepository->save($user)) {
                $this->authService->login($email, $password);
                header('Location: /');
                exit;
            }

            throw new Exception('Failed to save user.');
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            http_response_code(500);
            echo 'An error occurred during registration. Please try again later.';
        }
    }

    public function logout(array $data): void
    {
        $this->csrf->verifyToken($data['csrf_token'] ?? '');
        $this->authService->logout();
        header('Location: /login');
        exit;
    }
}