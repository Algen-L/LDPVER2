<?php
namespace App\Core;

class Router
{
    private $routes = [];

    public function add($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Basic cleanup of URI
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);

        // Remove script directory from URI
        // Remove script directory from URI (Case-insensitive for Windows)
        if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Remove index.php from URI if present
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10);
        }
        if ($uri === false || $uri === '') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                // Handler is usually "Controller@method"
                $parts = explode('@', $route['handler']);
                $controllerName = "App\\Controllers\\" . $parts[0];
                $actionName = $parts[1];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $actionName)) {
                        return $controller->$actionName();
                    }
                }
            }
        }

        // 404
        // 404
        echo "404 Not Found. Debug: Method=$method, URI=$uri";
        // var_dump($this->routes); // Uncomment if needed, but might be too large
    }
}
