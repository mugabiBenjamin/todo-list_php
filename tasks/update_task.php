<?php
session_start();
include_once '../connection.php';
include_once './tasks/task_validation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['name'], $_POST['csrf_token'])) {

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $id = (int) $_POST['id'];
    $name = sanitize_input($_POST['name']);
    $nameError = validateTaskName($name);

    if (!empty($nameError)) {
        header("Location: ../index.php?error=" . urlencode($nameError));
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE tasks SET name = :name WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header("Location: ../index.php?success=Task updated successfully");
        } else {
            header("Location: ../index.php?error=Failed to update task");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
