<?php
include_once '../config.php';

try {
    $tasks = [];
    $stmt = $pdo->prepare("SELECT * FROM tasks ORDER BY id DESC");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>