<?php

namespace App\Routes;

class Router
{
    private array $routes = [];

    public function get(string $uri, callable $handler): void
    {
        $this->routes['GET'][$uri] = $handler;
    }

    public function post(string $uri, callable $handler): void
    {
        $this->routes['POST'][$uri] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(\d+)', $pattern) . '$#';

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        require_once __DIR__ . '/../Views/Errors/404.php';
    }
}