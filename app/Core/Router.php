<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $controller)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }

    public function get($uri, $controller)
    {
        $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        $this->add('POST', $uri, $controller);
    }

    public function dispatch($uri, $method)
    {
        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches) && $route['method'] === strtoupper($method)) {
                array_shift($matches);

                $this->callAction(
                    ...explode('@', $route['controller']),
                    params: $matches
                );
                return;
            }
        }

        throw new \Exception('No route found for this URI.');
    }

    protected function callAction($controller, $action, $params = [])
    {

        $className = "App\\Controllers\\{$controller}";

        $controllerInstance = new $className;

        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception(
                "{$className} does not respond to the {$action} action."
            );
        }

        return call_user_func_array([$controllerInstance, $action], $params);
    }
}