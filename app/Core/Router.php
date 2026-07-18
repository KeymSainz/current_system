<?php
/**
 * Fix&Go — Router
 * Maps URL paths to controllers and actions.
 * Handles both API (JSON) and page (HTML) routes.
 */
namespace App\Core;

class Router
{
    private array $routes = [];

    /**
     * Register a route.
     * $method: 'GET', 'POST', or 'ANY'
     * $path:   e.g. '/api/login', '/dashboard', '/views/customer/orders'
     * $handler: 'ControllerClass@method'
     */
    public function add(string $method, string $path, string $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function any(string $path, string $handler): void
    {
        $this->add('ANY', $path, $handler);
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Support legacy _route param from .htaccess rewrites
        if (!empty($_GET['_route'])) {
            $uri = '/' . ltrim($_GET['_route'], '/');
        }

        // Strip base path if app is in a subdirectory
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . ltrim($uri ?: '/', '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            // Support basic {param} wildcards
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // remove full match
                [$class, $action] = explode('@', $route['handler']);
                $fullClass = '\\App\\Controllers\\' . $class;

                if (!class_exists($fullClass)) {
                    http_response_code(500);
                    echo "Controller not found: $fullClass";
                    exit;
                }

                $controller = new $fullClass();
                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    echo "Action not found: $action";
                    exit;
                }

                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        // No route matched
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => '404 Not Found']);
    }
}
