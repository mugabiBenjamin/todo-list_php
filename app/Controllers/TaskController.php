<?php

namespace App\Controllers;

use App\Interfaces\TaskRepositoryInterface;
use App\Helpers\CsrfGuard;
use App\Helpers\InputSanitizer;
use App\Helpers\RateLimiter;
use App\Models\Task;
use App\Validators\TaskValidator;
use App\Config\Paths;

class TaskController
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
        private readonly CsrfGuard              $csrf,
        private readonly InputSanitizer         $sanitizer,
        private readonly RateLimiter            $rateLimiter,
        private readonly TaskValidator          $validator,
    ) {}

    public function index(): void
    {
        $tasks = $this->repository->all();
        require Paths::views() . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . 'index.php';
    }

    public function create(): void
    {
        require Paths::views() . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . 'create.php';
    }

    public function store(array $data): void
    {
        if (!$this->rateLimiter->check('task_create', 10, 60)) {
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

        $this->repository->save(new Task(null, $name));
        header('Location: /');
        exit;
    }

    public function edit(string $id): void
    {
        $task = $this->repository->find((int) $id);

        if ($task === null) {
            http_response_code(404);
            require Paths::views() . DIRECTORY_SEPARATOR . 'Errors' . DIRECTORY_SEPARATOR . '404.php';
            return;
        }

        require Paths::views() . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . 'edit.php';
    }

    public function update(string $id, array $data): void
    {
        $this->csrf->verifyToken($data['csrf_token'] ?? '');

        $task = $this->repository->find((int) $id);

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
    }

    public function delete(string $id, array $data): void
    {
        $this->csrf->verifyToken($data['csrf_token'] ?? '');
        $this->repository->delete((int) $id);
        header('Location: /');
        exit;
    }
}