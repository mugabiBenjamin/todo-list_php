<?php

namespace App\Controllers;

use App\Interfaces\TaskRepositoryInterface;
use App\Services\AuthService;
use App\Helpers\CsrfGuard;
use App\Helpers\InputSanitizer;
use App\Helpers\RateLimiter;
use App\Models\Task;
use App\Validators\TaskValidator;
use App\Config\Paths;
use RuntimeException;

class TaskController
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
        private readonly AuthService             $authService,
        private readonly CsrfGuard               $csrf,
        private readonly InputSanitizer          $sanitizer,
        private readonly RateLimiter             $rateLimiter,
        private readonly TaskValidator           $validator,
    ) {}

    public function index(): void
    {
        $userId = $this->authService->userId();

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            $tasks = $this->repository->all($userId);
            require Paths::views() . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . 'index.php';
        } catch (RuntimeException $e) {
            http_response_code(500);
            require Paths::views() . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . '500.php';
        }
    }

    public function create(): void
    {
        require Paths::views() . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . 'create.php';
    }

    public function store(array $data): void
    {
        $userId = $this->authService->userId();

        if (!$userId) {
            http_response_code(401);
            exit;
        }

        if (!$this->rateLimiter->check('task_create_' . $userId, 10, 60)) {
            http_response_code(429);
            echo 'Too many requests. Please try again later.';
            return;
        }

        $this->csrf->verifyToken($data['csrf_token'] ?? '');

        $name = $this->sanitizer->sanitize($data['name'] ?? '');

        if (!$this->validator->validate(['name' => $name])) {
            http_response_code(400);
            echo $this->validator->firstError();
            return;
        }

        try {
            $this->repository->save(new Task(null, $name, false, $userId));
            header('Location: /');
            exit;
        } catch (RuntimeException $e) {
            http_response_code(500);
            echo 'An error occurred while saving the task.';
        }
    }

    public function edit(string $id): void
    {
        $userId = $this->authService->userId();

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            $task = $this->repository->find((int) $id, $userId);

            if ($task === null) {
                http_response_code(404);
                require Paths::views() . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . '404.php';
                return;
            }

            require Paths::views() . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . 'edit.php';
        } catch (RuntimeException $e) {
            http_response_code(500);
            require Paths::views() . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . '500.php';
        }
    }

    public function update(string $id, array $data): void
    {
        $userId = $this->authService->userId();

        if (!$userId) {
            http_response_code(401);
            exit;
        }

        $this->csrf->verifyToken($data['csrf_token'] ?? '');

        try {
            $task = $this->repository->find((int) $id, $userId);

            if ($task === null) {
                http_response_code(404);
                require Paths::views() . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . '404.php';
                return;
            }

            $name = $this->sanitizer->sanitize($data['name'] ?? '');

            if (!$this->validator->validate(['name' => $name])) {
                http_response_code(400);
                echo $this->validator->firstError();
                return;
            }

            $task->name      = $name;
            $task->completed = isset($data['completed']) && $data['completed'] === '1';

            $this->repository->update($task);
            header('Location: /');
            exit;
        } catch (RuntimeException $e) {
            http_response_code(500);
            echo 'An error occurred while updating the task.';
        }
    }

    public function delete(string $id, array $data): void
    {
        $userId = $this->authService->userId();

        if (!$userId) {
            http_response_code(401);
            exit;
        }

        $this->csrf->verifyToken($data['csrf_token'] ?? '');
        
        try {
            $this->repository->delete((int) $id, $userId);
            header('Location: /');
            exit;
        } catch (RuntimeException $e) {
            http_response_code(500);
            echo 'An error occurred while deleting the task.';
        }
    }
}