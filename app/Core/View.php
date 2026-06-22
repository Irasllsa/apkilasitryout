<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    private static array $shared = [];
    private static ?string $layout = null;
    private static array $sections = [];
    private static array $sectionStack = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = []): string
    {
        $file = BASE_PATH . '/app/Views/' . str_replace('.', '/', $template) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View tidak ditemukan: {$template}");
        }

        self::$layout = null;
        self::$sections = [];
        $data = array_merge(self::$shared, $data);

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        $content = ob_get_clean();

        if (self::$layout !== null) {
            $layoutFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', self::$layout) . '.php';
            self::$sections['content'] = $content;
            $sections = self::$sections;
            extract($data, EXTR_SKIP);
            ob_start();
            require $layoutFile;
            $content = ob_get_clean();
            self::$layout = null;
            self::$sections = [];
        }

        return $content;
    }

    // ---- API yang dipakai di dalam file view ----

    public static function extend(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function start(string $name): void
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function stop(): void
    {
        $name = array_pop(self::$sectionStack);
        if ($name !== null) {
            self::$sections[$name] = ob_get_clean();
        }
    }

    public static function section(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }
}
