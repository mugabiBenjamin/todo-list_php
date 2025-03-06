<?php

namespace App\Models;

use App\Database\DatabaseManager;
use PDO;

class Task
{
    public static function getAllTasks()
    {
        $db = DatabaseManager::getConnection();
        $stmt = $db->query("SELECT * FROM tasks ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTaskById($id)
    {
        $db = DatabaseManager::getConnection();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createTask($name)
    {
        $db = DatabaseManager::getConnection();
        $stmt = $db->prepare("INSERT INTO tasks (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    }

    public static function updateTask($id, $name)
    {
        $db = DatabaseManager::getConnection();
        $stmt = $db->prepare("UPDATE tasks SET name = :name WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public static function deleteTask($id)
    {
        $db = DatabaseManager::getConnection();
        $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function toggleTask($id)
    {
        $db = DatabaseManager::getConnection();
        $stmt = $db->prepare("UPDATE tasks SET completed = NOT completed WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}