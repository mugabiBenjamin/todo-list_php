<?php

namespace App\Middleware;

use App\Services\AuthService;

class AuthMiddleware
{
    public function __construct(private readonly AuthService $authService) {}

    public function handle(): void
    {
        if (!$this->authService->check()) {
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            header('Location: /login');
            exit;
        }
    }
}