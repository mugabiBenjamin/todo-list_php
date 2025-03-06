<?php

namespace App\Models;

use App\Database\DatabaseManager;
use PDO;

class Task
{
    public $id;
    public $name;
    public $completed;

    public function __construct($id = null, $name = null, $completed = 0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->completed = $completed;
    }

    public function save(DatabaseManager $db)
    {
        $stmt = $db->getConnection()->prepare("INSERT INTO tasks (name, completed) VALUES (?, ?)");
        $stmt->execute([$this->name, $this->completed]);
    }

    public function update(DatabaseManager $db)
    {
        $stmt = $db->getConnection()->prepare("UPDATE tasks SET name = ?, completed = ? WHERE id = ?");
        $stmt->execute([$this->name, $this->completed, $this->id]);
    }

    public static function all(DatabaseManager $db)
    {
        $stmt = $db->getConnection()->query("SELECT * FROM tasks");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(DatabaseManager $db, $id)
    {
        $stmt = $db->getConnection()->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function destroy(DatabaseManager $db, $id)
    {
        $stmt = $db->getConnection()->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
    }
}