<?php

namespace App\Repositories;

use App\Interfaces\DatabaseConnectionInterface;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use PDOException;
use RuntimeException;

class PdoTaskRepository implements TaskRepositoryInterface
{
    private const CACHE_PREFIX = 'task_cache_';

    public function __construct(private readonly DatabaseConnectionInterface $db) {}

    public function all(int $userId): array
    {
        $cacheKey = self::CACHE_PREFIX . $userId;
        
        if (isset($_SESSION[$cacheKey])) {
            return $_SESSION[$cacheKey];
        }

        try {
            $stmt = $this->db->getConnection()->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY id ASC');
            $stmt->execute([$userId]);
            $tasks = array_map(fn(array $row) => $this->hydrate($row), $stmt->fetchAll());

            $_SESSION[$cacheKey] = $tasks;

            return $tasks;
        } catch (PDOException $e) {
            error_log('Database error fetching tasks: ' . $e->getMessage());
            throw new RuntimeException('Failed to retrieve tasks.');
        }
    }

    public function find(int $id, int $userId): ?Task
    {
        try {
            $stmt = $this->db->getConnection()->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $userId]);
            $row = $stmt->fetch();
            
            return $row ? $this->hydrate($row) : null;
        } catch (PDOException $e) {
            error_log('Database error finding task: ' . $e->getMessage());
            throw new RuntimeException('Failed to retrieve task.');
        }
    }

    public function save(Task $task): void
    {
        try {
            $stmt = $this->db->getConnection()->prepare(
                'INSERT INTO tasks (name, completed, user_id) VALUES (?, ?, ?)'
            );
            $stmt->execute([$task->name, $task->completed ? 'true' : 'false', $task->user_id]);
            $this->invalidateCache($task->user_id);
        } catch (PDOException $e) {
            error_log('Database error saving task: ' . $e->getMessage());
            throw new RuntimeException('Failed to save task.');
        }
    }

    public function update(Task $task): void
    {
        try {
            $stmt = $this->db->getConnection()->prepare(
                'UPDATE tasks SET name = ?, completed = ? WHERE id = ? AND user_id = ?'
            );
            $stmt->execute([$task->name, $task->completed ? 'true' : 'false', $task->id, $task->user_id]);
            $this->invalidateCache($task->user_id);
        } catch (PDOException $e) {
            error_log('Database error updating task: ' . $e->getMessage());
            throw new RuntimeException('Failed to update task.');
        }
    }

    public function delete(int $id, int $userId): void
    {
        try {
            $stmt = $this->db->getConnection()->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $userId]);
            $this->invalidateCache($userId);
        } catch (PDOException $e) {
            error_log('Database error deleting task: ' . $e->getMessage());
            throw new RuntimeException('Failed to delete task.');
        }
    }

    private function hydrate(array $row): Task
    {
        return new Task(
            id:        (int) $row['id'],
            name:      $row['name'],
            completed: $row['completed'] === true || $row['completed'] === 't',
            user_id:   (int) $row['user_id']
        );
    }

    private function invalidateCache(int $userId): void
    {
        unset($_SESSION[self::CACHE_PREFIX . $userId]);
    }
}