<?php

use App\Controllers\TaskController;
use App\Database\DatabaseManager;
use App\Helpers\Security;

// Load database configuration
$config = require __DIR__ . '/../Config/Database.php';

try {
    // Initialize database connection
    $db = new DatabaseManager($config);
    $controller = new TaskController($db);

    // Parse the request
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($uri === '/' && $method === 'GET') {
        $controller->index();
    } elseif ($uri === '/create' && $method === 'GET') {
        $controller->create();
    } elseif ($uri === '/tasks' && $method === 'POST') {
        $controller->store($_POST);
        header('Location: /');
        exit;
    } elseif (preg_match('/\/edit\/(\d+)/', $uri, $matches) && $method === 'GET') {
        $id = filter_var($matches[1], FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            echo "Invalid task ID format";
            exit;
        }
        $controller->edit($id);
    } elseif (preg_match('/\/update\/(\d+)/', $uri, $matches) && $method === 'POST') {
        $id = filter_var($matches[1], FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            echo "Invalid task ID format";
            exit;
        }
        $controller->update($id, $_POST);
        header('Location: /');
        exit;
    } elseif (preg_match('/\/delete\/(\d+)/', $uri, $matches) && $method === 'POST') {
        $id = filter_var($matches[1], FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            echo "Invalid task ID format";
            exit;
        }
        if(!isset($_POST['csrf_token'])){
            http_response_code(400);
            echo "Missing CSRF token";
            exit;
        }
        $controller->delete($id, $_POST['csrf_token']);
        header('Location: /');
        exit;
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "An internal server error occurred. Please try again later.";
}