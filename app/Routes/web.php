<?php

use App\Controllers\TaskController;
use App\Controllers\AuthController;
use App\Database\DatabaseManager;
use App\Helpers\CsrfGuard;
use App\Helpers\InputSanitizer;
use App\Helpers\RateLimiter;
use App\Helpers\PasswordHasher;
use App\Repositories\PdoTaskRepository;
use App\Repositories\PdoUserRepository;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
use App\Routes\Router;
use App\Validators\TaskValidator;
use App\Validators\AuthValidator;
use App\Config\Database;

$db             = new DatabaseManager(Database::config());
$taskRepository = new PdoTaskRepository($db);
$userRepository = new PdoUserRepository($db);

$csrf           = new CsrfGuard();
$sanitizer      = new InputSanitizer();
$rateLimiter    = new RateLimiter();
$hasher         = new PasswordHasher();

$authService    = new AuthService($userRepository, $hasher);
$authMiddleware = new AuthMiddleware($authService);

$taskController = new TaskController(
    repository:  $taskRepository,
    csrf:        $csrf,
    sanitizer:   $sanitizer,
    rateLimiter: $rateLimiter,
    validator:   new TaskValidator(),
);

$authController = new AuthController(
    authService:    $authService,
    userRepository: $userRepository,
    hasher:         $hasher,
    csrf:           $csrf,
    sanitizer:      $sanitizer,
    rateLimiter:    $rateLimiter,
    validator:      new AuthValidator(),
);

$router = new Router();

$router->get('/login',        fn()    => $authController->showLogin());
$router->post('/login',       fn()    => $authController->login($_POST));
$router->get('/register',     fn()    => $authController->showRegister());
$router->post('/register',    fn()    => $authController->register($_POST));
$router->post('/logout',      fn()    => $authController->logout($_POST));

$router->get('/',             fn()    => $taskController->index(),            [$authMiddleware]);
$router->get('/create',       fn()    => $taskController->create(),           [$authMiddleware]);
$router->post('/tasks',       fn()    => $taskController->store($_POST),      [$authMiddleware]);
$router->get('/edit/{id}',    fn($id) => $taskController->edit($id),          [$authMiddleware]);
$router->post('/update/{id}', fn($id) => $taskController->update($id, $_POST),[$authMiddleware]);
$router->post('/delete/{id}', fn($id) => $taskController->delete($id, $_POST),[$authMiddleware]);

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);