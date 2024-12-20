<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../connection.php';
include_once 'task_validation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = htmlspecialchars(sanitize_input($_POST['name']));

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $name = sanitize_input($_POST['name']);
    $nameError = validateTaskName($name);

    if (!empty($nameError)) {
        header("Location: ../index.php?error=" . urlencode($nameError));
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            header("Location: ../index.php?success=Task added successfully");
        } else {
            header("Location: ../index.php?error=Failed to add task");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
