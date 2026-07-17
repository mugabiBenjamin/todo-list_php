<?php

namespace App\Interfaces;

use App\Models\Task;

interface TaskRepositoryInterface
{
    public function all(): array;
    public function find(int $id): ?Task;
    public function save(Task $task): void;
    public function update(Task $task): void;
    public function delete(int $id): void;
}