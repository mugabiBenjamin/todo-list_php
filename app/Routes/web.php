<?php

use App\Controllers\TaskController;

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET' => [
        '/tasks' => 'TaskController@index',
        '/tasks/create' => 'TaskController@create',
        '/tasks/edit' => 'TaskController@edit',
        '/tasks/toggle' => 'TaskController@toggle',
        '/tasks/delete' => 'TaskController@delete',
    ],
    'POST' => [
        '/tasks/store' => 'TaskController@store',
        '/tasks/update' => 'TaskController@update',
    ],
];

if (isset($routes[$requestMethod])) {
    foreach ($routes[$requestMethod] as $route => $action) {
        if (strpos($requestUri, $route) === 0) {
            $parts = explode('@', $action);
            $controllerName = 'App\\Controllers\\' . $parts[0];
            $methodName = $parts[1];
            $controller = new $controllerName();

            if (strpos($requestUri, 'edit') !== false || strpos($requestUri, 'delete') !== false || strpos($requestUri, 'toggle') !== false || strpos($requestUri, 'update') !== false){
                $uriParts = explode('=', $requestUri);
                $id = $uriParts[1];
                $controller->$methodName($id);
            } else {
                $controller->$methodName();
            }

            exit;
        }
    }
}

echo "404 Not Found";