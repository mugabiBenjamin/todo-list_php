<?php

namespace App\Controllers;

use App\Models\Task;
use App\Database\DatabaseManager;
use App\Helpers\Security;

class TaskController
{
    private $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function index()
    {
        $tasks = Task::all($this->db);
        require_once __DIR__ . '/../Views/Tasks/index.php';
    }

    public function create()
    {
        require_once __DIR__ . '/../Views/Tasks/create.php';
    }

    public function store($data)
    {
        Security::verifyCsrfToken($data['csrf_token']);
        $name = Security::sanitize($data['name']);
        $task = new Task(null, $name, 0);
        $task->save($this->db);
    }

    public function edit($id)
    {
        $task = Task::find($this->db, $id);
        require_once __DIR__ . '/../Views/Tasks/edit.php';
    }

    public function update($id, $data)
    {
        Security::verifyCsrfToken($data['csrf_token']);
        $name = Security::sanitize($data['name']);
        $completed = isset($data['completed']) ? 1 : 0;
        $task = new Task($id, $name, $completed);
        $task->update($this->db);
    }

    public function delete($id, $token)
    {
        Security::verifyCsrfToken($token);
        Task::destroy($this->db, $id);
    }
}