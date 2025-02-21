<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'], $_GET['csrf_token'])) {
    
    if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    
    $id = (int)$_GET['id'];

    if ($id <= 0) {
        header("Location: ../index.php?error=Invalid task ID");
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            header("Location: ../index.php?success=Task deleted successfully.");
        } else {
            header("Location: ../index.php?error=Task not found.");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}