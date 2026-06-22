<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function get(string $path, mixed $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, mixed $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, mixed $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, mixed $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    /**
     * Kelompokkan route dengan prefix & middleware bersama.
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function add(string $method, string $path, mixed $handler, array $middleware): void
    {
        $prefix = '';
        $groupMw = [];
        foreach ($this->groupStack as $group) {
            $prefix .= $group['prefix'] ?? '';
            $groupMw = array_merge($groupMw, $group['middleware'] ?? []);
        }
        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        $fullPath = $fullPath === '' ? '/' : $fullPath;

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => array_merge($groupMw, $middleware),
            'regex' => $this->compile($fullPath),
        ];
    }

    private function compile(string $path): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    /**
     * @return array{0:array,1:array}|null  [route, params] atau null jika tidak cocok
     */
    public function match(string $method, string $path): ?array
    {
        $methodMismatch = false;
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                if ($route['method'] !== $method) {
                    $methodMismatch = true;
                    continue;
                }
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }
                return [$route, $params];
            }
        }
        if ($methodMismatch) {
            http_response_code(405);
        }
        return null;
    }
}
