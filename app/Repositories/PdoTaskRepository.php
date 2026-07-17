<?php

namespace App\Repositories;

use App\Interfaces\DatabaseConnectionInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class PdoTaskRepository implements TaskRepositoryInterface
{
    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function all(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT * FROM tasks ORDER BY id ASC');
        return array_map(fn(array $row) => $this->hydrate($row), $stmt->fetchAll());
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
    }

    public function update(Task $task): void
    {
        $stmt = $this->db->getConnection()->prepare(
            'UPDATE tasks SET name = ?, completed = ? WHERE id = ?'
        );
        $stmt->execute([$task->name, $task->completed ? 'true' : 'false', $task->id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->getConnection()->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function hydrate(array $row): Task
    {
        return new Task(
            id:        (int) $row['id'],
            name:      $row['name'],
            completed: $row['completed'] === true || $row['completed'] === 't',
        );
    }
}