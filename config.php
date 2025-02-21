<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? null;
$username = $_ENV['DB_USERNAME'] ?? null;
$password = $_ENV['DB_PASSWORD'] ?? null;
$dbname = $_ENV['DB_NAME'] ?? null;
$apiKey = $_ENV['API_KEY'] ?? null;

if (!$host || !$username || !$dbname) {
    die("Missing required environment variables. Please check your .env file.");
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Database connection established successfully.";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
