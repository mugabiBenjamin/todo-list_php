<?php

namespace App\Repositories;

use App\Interfaces\DatabaseConnectionInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class PdoTaskRepository implements TaskRepositoryInterface
{
    private const CACHE_KEY = 'task_cache';

    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function all(): array
    {
        if (isset($_SESSION[self::CACHE_KEY])) {
            return $_SESSION[self::CACHE_KEY];
        }

        $stmt  = $this->db->getConnection()->query('SELECT * FROM tasks ORDER BY id ASC');
        $tasks = array_map(fn(array $row) => $this->hydrate($row), $stmt->fetchAll());

        $_SESSION[self::CACHE_KEY] = $tasks;

        return $tasks;
    }

    public function find(int $id): ?Task
    {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function save(Task $task): void
    {
        $stmt = $this->db->getConnection()->prepare(
            'INSERT INTO tasks (name, completed) VALUES (?, ?)'
        );
        $stmt->execute([$task->name, $task->completed ? 'true' : 'false']);
        $this->invalidateCache();
    }

    public function update(Task $task): void
    {
        $stmt = $this->db->getConnection()->prepare(
            'UPDATE tasks SET name = ?, completed = ? WHERE id = ?'
        );
        $stmt->execute([$task->name, $task->completed ? 'true' : 'false', $task->id]);
        $this->invalidateCache();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->getConnection()->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $this->invalidateCache();
    }

    private function hydrate(array $row): Task
    {
        return new Task(
            id:        (int) $row['id'],
            name:      $row['name'],
            completed: $row['completed'] === true || $row['completed'] === 't',
        );
    }

    private function invalidateCache(): void
    {
        unset($_SESSION[self::CACHE_KEY]);
    }
}