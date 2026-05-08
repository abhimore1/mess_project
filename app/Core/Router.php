<?php
/**
 * HTTP Router — supports GET, POST, PUT, DELETE with middleware groups.
 */
class Router
{
    private array $routes    = [];
    private array $groupMiddleware = [];
    private string $groupPrefix    = '';

    public function get(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group(array $options, callable $callback): void
    {
        $prevPrefix     = $this->groupPrefix;
        $prevMiddleware = $this->groupMiddleware;

        $this->groupPrefix     .= $options['prefix'] ?? '';
        $this->groupMiddleware  = array_merge($this->groupMiddleware, $options['middleware'] ?? []);

        $callback($this);

        $this->groupPrefix     = $prevPrefix;
        $this->groupMiddleware = $prevMiddleware;
    }

    private function addRoute(string $method, string $path, string|array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $this->groupPrefix . $path,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Normalize URI — strip base path (/mess/public)
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $uri      = '/' . ltrim(substr($uri, strlen($basePath)), '/');
        $uri      = strtok($uri, '?') ?: '/';  // strip query string

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;

            $pattern = $this->buildPattern($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middleware chain
                foreach ($route['middleware'] as $mw) {
                    $mwClass = 'App\\Middleware\\' . $mw;
                    if (!str_ends_with($mwClass, 'Middleware')) {
                        $mwClass .= 'Middleware';
                    }
                    (new $mwClass())->handle();
                }

                // Dispatch controller
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        $this->render404();
    }

    private function buildPattern(string $path): string
    {
        // Convert {param} → named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function callHandler(string|array $handler, array $params): void
    {
        if (is_string($handler)) {
            [$class, $method] = explode('@', $handler);
        } else {
            [$class, $method] = $handler;
        }

        $fullClass = 'App\\Controllers\\' . $class;
        if (!class_exists($fullClass)) {
            throw new \RuntimeException("Controller not found: $fullClass");
        }

        $controller = new $fullClass();
        $controller->$method(...array_values($params));
    }

    private function render404(): void
    {
        if (file_exists(APP_PATH . '/Views/errors/404.php')) {
            include APP_PATH . '/Views/errors/404.php';
        } else {
            echo '<h1>404 — Page Not Found</h1>';
        }
    }
}
