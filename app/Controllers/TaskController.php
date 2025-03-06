<?php

namespace App\Controllers;

use App\Helpers\Security;
use App\Models\Task;

class TaskController
{
    public function index()
    {
        $tasks = Task::getAllTasks();
        include __DIR__ . '/../Views/Tasks/index.php';
    }

    public function create()
    {
        include __DIR__ . '/../Views/Tasks/create.php';
    }

    public function store()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'])) {
                die('CSRF token verification failed.');
            }
            $name = Security::sanitizeInput($_POST['name']);
            Task::createTask($name);
            header("Location: /tasks");
            exit;
        }
    }

    public function edit($id)
    {
        $task = Task::getTaskById($id);
        include __DIR__ . '/../Views/Tasks/edit.php';
    }

    public function update($id)
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'])) {
                die('CSRF token verification failed.');
            }
            $name = Security::sanitizeInput($_POST['name']);
            Task::updateTask($id, $name);
            header("Location: /tasks");
            exit;
        }
    }

    public function delete($id)
    {
        Task::deleteTask($id);
        header("Location: /tasks");
        exit;
    }

    public function toggle($id)
    {
        Task::toggleTask($id);
        header("Location: /tasks");
        exit;
    }
}