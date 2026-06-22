<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private static ?array $user = null;

    public static function attempt(string $username, string $password): bool
    {
        $row = Database::first(
            'SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1',
            [$username, $username]
        );

        if ($row === null || !password_verify($password, $row['password'])) {
            return false;
        }

        // Rehash bila perlu
        if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
            Database::run('UPDATE users SET password = ? WHERE id = ?', [
                password_hash($password, PASSWORD_DEFAULT),
                $row['id'],
            ]);
        }

        self::login($row);
        return true;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Database::run('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
        self::$user = self::sanitize($user);
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }
        $id = Session::get('user_id');
        if ($id === null) {
            return null;
        }
        $row = Database::first('SELECT * FROM users WHERE id = ? AND is_active = 1 LIMIT 1', [$id]);
        if ($row === null) {
            self::logout();
            return null;
        }
        self::$user = self::sanitize($row);
        return self::$user;
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u !== null ? (int) $u['id'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::remove('user_id');
        Session::destroy();
    }

    private static function sanitize(array $user): array
    {
        unset($user['password']);
        return $user;
    }
}
