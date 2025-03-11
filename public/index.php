<?php

// public/index.php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

define('APP_ROOT', dirname(__DIR__));

if (basename($_SERVER['SCRIPT_FILENAME']) !== 'index.php') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access forbidden');
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600);

header_remove('X-Powered-By');

require_once APP_ROOT . '/vendor/autoload.php';

ob_start();

try {
    $dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
    $dotenv->safeLoad();

    session_start(); // Start session here

    require_once APP_ROOT . '/app/Routes/web.php';
} catch (Exception $e) {
    error_log($e->getMessage());
    ob_clean();
    http_response_code(500);
    include APP_ROOT . '/app/Views/Errors/500.php'; // Create this file
}

ob_end_flush();
