<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function get($uri, $controller)
    {
        $this->addRoute('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        $this->addRoute('POST', $uri, $controller);
    }

    private function addRoute($method, $uri, $controller)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }

    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                
                $action = explode('@', $route['controller']);
                // Support sub-namespaces like Sales\CustomerController
                $controllerClass = "App\\Controllers\\" . str_replace('/', '\\', $action[0]);
                $methodName = $action[1];

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $methodName)) {
                        return $controller->$methodName();
                    } else {
                        $this->abort(404);
                    }
                } else {
                    $this->abort(404);
                }
            }
        }
        $this->abort();
    }

    protected function abort($code = 404, $message = '')
    {
        http_response_code($code);
        echo "Sorry. Not found. " . $message;
        die();
    }
}
