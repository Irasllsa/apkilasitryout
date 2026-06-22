<?php
declare(strict_types=1);

namespace App\Core;

final class App
{
    private static array $config = [];
    private Router $router;
    private Request $request;

    public function __construct(array $config)
    {
        self::$config = $config;
        $this->router = new Router();
        $this->request = new Request();

        Session::start();

        if (!empty($config['db'])) {
            Database::connect($config['db']);
        }

        // Data yang dibagikan ke semua view
        View::share('appName', self::config('app.name', 'TemanJuara'));
        View::share('currentUser', Auth::user());

        $this->loadRoutes();
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$config;
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        return $value;
    }

    private function loadRoutes(): void
    {
        $router = $this->router;
        require BASE_PATH . '/routes/web.php';
    }

    public function run(): void
    {
        $method = $this->request->method();
        $path = $this->request->path();

        $matched = $this->router->match($method, $path);

        if ($matched === null) {
            $this->abort(http_response_code() === 405 ? 405 : 404);
            return;
        }

        [$route, $params] = $matched;

        // Verifikasi CSRF untuk request yang mengubah data
        Csrf::verifyRequest();

        // Middleware
        foreach ($route['middleware'] as $mw) {
            if (!$this->runMiddleware($mw)) {
                return;
            }
        }

        $this->dispatch($route['handler'], $params);
    }

    private function runMiddleware(string $mw): bool
    {
        [$name, $arg] = array_pad(explode(':', $mw, 2), 2, null);

        switch ($name) {
            case 'auth':
                if (!Auth::check()) {
                    Session::flash('error', 'Silakan masuk terlebih dahulu.');
                    redirect('login');
                }
                return true;

            case 'guest':
                if (Auth::check()) {
                    redirect('dashboard');
                }
                return true;

            case 'role':
                $roles = array_map('trim', explode(',', (string) $arg));
                if (!Auth::check()) {
                    redirect('login');
                }
                if (!in_array(Auth::user()['role'], $roles, true)) {
                    $this->abort(403);
                    return false;
                }
                return true;

            default:
                return true;
        }
    }

    private function dispatch(mixed $handler, array $params): void
    {
        if (is_callable($handler)) {
            $output = $handler(...array_values($params));
            $this->emit($output);
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            $output = $controller->$action(...array_values($params));
            $this->emit($output);
            return;
        }

        $this->abort(500);
    }

    private function emit(mixed $output): void
    {
        if (is_string($output)) {
            echo $output;
        } elseif (is_array($output)) {
            header('Content-Type: application/json');
            echo json_encode($output);
        }
    }

    private function abort(int $code): void
    {
        http_response_code($code);
        $view = 'errors.' . $code;
        $file = BASE_PATH . '/app/Views/errors/' . $code . '.php';
        if (is_file($file)) {
            echo View::render($view);
        } else {
            echo "<h1>{$code}</h1>";
        }
    }
}
