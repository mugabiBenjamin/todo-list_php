<?php

namespace App\Routes;

use Exception;

class Router
{
    private array $routes = [];

    public function get(string $uri, callable $handler, array $middlewares = []): void
    {
        $this->routes['GET'][$uri] = [
            'handler'     => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function post(string $uri, callable $handler, array $middlewares = []): void
    {
        $this->routes['POST'][$uri] = [
            'handler'     => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        try {
            foreach ($this->routes[$method] ?? [] as $pattern => $route) {
                $regex = '#^' . preg_replace('#\{(\w+)\}#', '(\d+)', $pattern) . '$#';

                if (preg_match($regex, $uri, $matches)) {
                    array_shift($matches);
                    
                    foreach ($route['middlewares'] as $middleware) {
                        $middleware->handle();
                    }

                    call_user_func_array($route['handler'], $matches);
                    return;
                }
            }

            http_response_code(404);
            require_once __DIR__ . '/../Views/Errors/404.php';
        } catch (Exception $e) {
            error_log('Routing error: ' . $e->getMessage());
            http_response_code(500);
            require_once __DIR__ . '/../Views/Errors/500.php';
        }
    }
}