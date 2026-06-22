<?php
declare(strict_types=1);

use App\Core\App;
use App\Core\Session;

/**
 * Escape HTML untuk output aman.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Ambil config dengan dot-notation, mis. config('app.name').
 */
function config(string $key, mixed $default = null): mixed
{
    return App::config($key, $default);
}

/**
 * Bangun URL absolut berbasis base_url.
 */
function url(string $path = ''): string
{
    $base = rtrim((string) config('app.base_url', '/'), '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * URL ke aset statis.
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Redirect ke path internal lalu hentikan eksekusi.
 */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Ambil nilai dari $_POST/$_GET dengan default.
 */
function input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Token CSRF saat ini.
 */
function csrf_token(): string
{
    return \App\Core\Csrf::token();
}

/**
 * Field input hidden CSRF.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/**
 * Set / ambil flash message.
 */
function flash(?string $key = null, mixed $value = null): mixed
{
    if ($key === null) {
        return null;
    }
    if ($value !== null) {
        Session::flash($key, $value);
        return null;
    }
    return Session::getFlash($key);
}

/**
 * User yang sedang login (array) atau null.
 */
function auth(): ?array
{
    return \App\Core\Auth::user();
}

/**
 * Cek apakah user punya salah satu role.
 */
function has_role(string ...$roles): bool
{
    $user = auth();
    return $user !== null && in_array($user['role'], $roles, true);
}

/**
 * Render view dan kembalikan string.
 */
function view(string $template, array $data = []): string
{
    return \App\Core\View::render($template, $data);
}

/**
 * Format tanggal Indonesia sederhana.
 */
function tgl_id(?string $datetime, bool $withTime = false): string
{
    if (empty($datetime)) {
        return '-';
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return '-';
    }
    $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $out = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    if ($withTime) {
        $out .= ' ' . date('H:i', $ts);
    }
    return $out;
}

/**
 * Hasilkan string acak aman.
 */
function random_token(int $length = 32): string
{
    return bin2hex(random_bytes(max(8, (int) ($length / 2))));
}
