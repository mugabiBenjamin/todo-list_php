<?php
session_start();
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        header("Location: ../index.php?error=Invalid task ID");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = NOT completed WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            header("Location: ../index.php");
        } else {
            header("Location: ../index.php?error=Failed to update task status");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
    exit();
}