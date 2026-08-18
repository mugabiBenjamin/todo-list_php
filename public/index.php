<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

define('APP_ROOT', dirname(__DIR__));

if (basename($_SERVER['SCRIPT_FILENAME']) !== 'index.php') {
    http_response_code(403);
    exit('Access forbidden');
}

$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
    || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', $isSecure ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600);

header_remove('X-Powered-By');

require_once APP_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

ob_start();

try {
    if (file_exists(APP_ROOT . DIRECTORY_SEPARATOR . '.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
        $dotenv->load();
    }

    echo '<pre>';
    echo 'DB_HOST: [' . ($_ENV['DB_HOST'] ?? 'NOT SET') . ']' . PHP_EOL;
    echo 'DB_PORT: [' . ($_ENV['DB_PORT'] ?? 'NOT SET') . ']' . PHP_EOL;
    echo 'DB_USER: [' . ($_ENV['DB_USER'] ?? 'NOT SET') . ']' . PHP_EOL;
    echo 'DB_NAME: [' . ($_ENV['DB_NAME'] ?? 'NOT SET') . ']' . PHP_EOL;
    echo 'DB_HOST $_ENV: [' . ($_ENV['DB_HOST'] ?? 'NOT SET') . ']' . PHP_EOL;
    echo 'DB_HOST $_SERVER: [' . ($_SERVER['DB_HOST'] ?? 'NOT SET') . ']' . PHP_EOL;
    echo 'DB_HOST getenv: [' . (getenv('DB_HOST') ?: 'NOT SET') . ']' . PHP_EOL;
    echo '</pre>';
    exit;

    session_start();

    $secureHeaders = [
        'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; connect-src 'self';",
        'X-Content-Type-Options'  => 'nosniff',
        'X-Frame-Options'         => 'DENY',
        'X-XSS-Protection'        => '1; mode=block',
        'Referrer-Policy'         => 'strict-origin-when-cross-origin',
    ];

    foreach ($secureHeaders as $header => $value) {
        header("{$header}: {$value}");
    }

    require_once APP_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'web.php';

} catch (Exception $e) {
    error_log($e->getMessage());
    ob_clean();
    http_response_code(500);
    // TEMPORARY - remove after debugging
    echo '<pre style="padding:2rem;font-size:1rem;">' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

ob_end_flush();