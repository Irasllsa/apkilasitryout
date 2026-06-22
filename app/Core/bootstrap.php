<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
| Mendaftarkan autoloader, memuat konfigurasi, menyalakan sesi, lalu
| menjalankan router. Jika aplikasi belum terpasang (config belum ada),
| pengguna diarahkan ke wizard instalasi.
*/

// ---- Autoloader sederhana (PSR-4 untuk namespace App\) ----
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

// ---- Helper global ----
require BASE_PATH . '/app/Core/helpers.php';

// ---- Cek instalasi ----
$configFile = BASE_PATH . '/config/config.php';
$installLock = BASE_PATH . '/config/installed.lock';

if (!is_file($configFile) || !is_file($installLock)) {
    // Belum terpasang -> arahkan ke installer (kecuali sedang di installer)
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/install') === false) {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/') . '/install/');
        exit;
    }
}

$config = is_file($configFile) ? require $configFile : [];

// ---- Inisialisasi aplikasi ----
use App\Core\App;

$app = new App($config);
$app->run();
