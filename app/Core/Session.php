<?php
declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => (($_SERVER['HTTPS'] ?? '') === 'on'),
            ]);
            session_name('TJSESID');
            session_start();
        }
        // Bersihkan flash lama (yang sudah ditandai untuk dihapus)
        if (!empty($_SESSION['_flash_old'])) {
            foreach ($_SESSION['_flash_old'] as $k) {
                unset($_SESSION['_flash'][$k]);
            }
        }
        $_SESSION['_flash_old'] = array_keys($_SESSION['_flash'] ?? []);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
        // Jangan langsung dihapus pada request ini
        if (isset($_SESSION['_flash_old']) && in_array($key, $_SESSION['_flash_old'], true)) {
            $_SESSION['_flash_old'] = array_diff($_SESSION['_flash_old'], [$key]);
        }
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
