<?php

use App\Controllers\TaskController;
use App\Database\DatabaseManager;
use App\Helpers\CsrfGuard;
use App\Helpers\InputSanitizer;
use App\Helpers\RateLimiter;
use App\Repositories\PdoTaskRepository;
use App\Routes\Router;
use App\Validators\TaskValidator;
use App\Config\Database;

$db         = new DatabaseManager(Database::config());
$repository = new PdoTaskRepository($db);
$controller = new TaskController(
    repository:  $repository,
    csrf:        new CsrfGuard(),
    sanitizer:   new InputSanitizer(),
    rateLimiter: new RateLimiter(),
    validator:   new TaskValidator(),
);

$router = new Router();

$router->get('/',             fn()     => $controller->index());
$router->get('/create',       fn()     => $controller->create());
$router->post('/tasks',       fn()     => $controller->store($_POST));
$router->get('/edit/{id}',    fn($id)  => $controller->edit($id));
$router->post('/update/{id}', fn($id)  => $controller->update($id, $_POST));
$router->post('/delete/{id}', fn($id)  => $controller->delete($id, $_POST));

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($method, $uri);