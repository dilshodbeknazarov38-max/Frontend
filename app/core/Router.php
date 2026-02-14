<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, string $action): void
    {
        $this->addRoute('GET', $pattern, $action);
    }

    public function post(string $pattern, string $action): void
    {
        $this->addRoute('POST', $pattern, $action);
    }

    public function any(string $pattern, string $action): void
    {
        $this->addRoute('GET', $pattern, $action);
        $this->addRoute('POST', $pattern, $action);
    }

    public function options(string $pattern, string $action): void
    {
        $this->addRoute('OPTIONS', $pattern, $action);
    }

    private function addRoute(string $method, string $pattern, string $action): void
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . rtrim($regex, '/') . '/?$#';
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'regex' => $regex,
            'action' => $action,
        ];
    }

    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';
        $routes = $this->routes[$method] ?? [];
        foreach ($routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->runAction($route['action'], $params);
            }
        }
        http_response_code(404);
        echo view('errors/404', ['title' => 'Sahifa topilmadi']);
        return null;
    }

    private function runAction(string $action, array $params)
    {
        if (strpos($action, '@') === false) {
            throw new \InvalidArgumentException('Invalid route action');
        }
        [$controllerName, $method] = explode('@', $action);
        $controllerClass = 'App\\Controllers\\' . $controllerName;
        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller {$controllerClass} not found");
        }
        $controller = new $controllerClass();
        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$method} not found on controller {$controllerClass}");
        }
        return call_user_func_array([$controller, $method], $params);
    }
}
