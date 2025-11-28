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
            // Convert route URI to regex
            // e.g., /vehicles/edit/{id} -> #^/vehicles/edit/([^/]+)$#
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches) && $route['method'] === strtoupper($method)) {
                array_shift($matches); // Remove full match

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
    // Nombre completo de la clase
    $className = "App\\Controllers\\{$controller}";

    // Crear instancia del controlador
    $controllerInstance = new $className;

    // Verificar que el método exista
    if (!method_exists($controllerInstance, $action)) {
        // IMPORTANTE: aquí usamos el NOMBRE de la clase, no el objeto
        throw new \Exception(
            "{$className} does not respond to the {$action} action."
        );
    }

    // Llamar al método con los parámetros de la ruta
    return call_user_func_array([$controllerInstance, $action], $params);
}

}
