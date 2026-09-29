<?php

namespace App\Interfaces;

use App\Models\Task;

interface TaskRepositoryInterface
{
    public function all(int $userId): array;
    public function find(int $id, int $userId): ?Task;
    public function save(Task $task): void;
    public function update(Task $task): void;
    public function delete(int $id, int $userId): void;
}