<?php
declare(strict_types=1);

namespace App\Core;

final class Settings
{
    private static ?array $cache = null;

    private static function load(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                foreach (Database::all('SELECT nama_key, nilai FROM pengaturan') as $row) {
                    self::$cache[$row['nama_key']] = $row['nilai'];
                }
            } catch (\Throwable $e) {
                self::$cache = [];
            }
        }
        return self::$cache;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::load();
        $val = $all[$key] ?? $default;
        return ($val === null || $val === '') ? $default : $val;
    }

    public static function all(): array
    {
        return self::load();
    }

    public static function set(string $key, ?string $value): void
    {
        Database::run(
            'INSERT INTO pengaturan (nama_key, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)',
            [$key, $value]
        );
        self::$cache = null;
    }
}
