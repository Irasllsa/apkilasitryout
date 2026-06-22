<?php
/**
 * TemanJuara - Installer Wizard
 * Self-contained, dijalankan sekali saat instalasi awal.
 */
declare(strict_types=1);
session_start();

define('ROOT', dirname(__DIR__));
define('CONFIG_FILE', ROOT . '/config/config.php');
define('LOCK_FILE', ROOT . '/config/installed.lock');

require __DIR__ . '/view.php'; // helper tampilan (render_shell, render_page)

// Jika sudah terpasang, blokir akses installer
if (is_file(LOCK_FILE) && (($_GET['force'] ?? '') !== '1')) {
    http_response_code(403);
    render_shell('Sudah Terpasang', '<div class="text-center py-8">
        <div class="mx-auto w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="text-xl font-bold text-slate-800">Aplikasi sudah terpasang</h2>
        <p class="text-slate-500 mt-2">Demi keamanan, hapus folder <code class="bg-slate-100 px-1 rounded">/install</code> dari server.</p>
        <a href="../" class="inline-block mt-6 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Buka Aplikasi</a>
    </div>');
    exit;
}

$step = $_GET['step'] ?? 'welcome';
$errors = [];

// ---------------------------------------------------------------------
// Aksi POST
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'db') {
        $db = [
            'host' => trim($_POST['db_host'] ?? 'localhost'),
            'port' => trim($_POST['db_port'] ?? '3306'),
            'name' => trim($_POST['db_name'] ?? ''),
            'user' => trim($_POST['db_user'] ?? ''),
            'pass' => $_POST['db_pass'] ?? '',
        ];
        $base = trim($_POST['base_url'] ?? '');
        $errors = test_db($db);
        if (empty($errors)) {
            $_SESSION['install_db'] = $db;
            $_SESSION['install_base'] = $base;
            header('Location: ?step=admin');
            exit;
        }
        $step = 'db';
    }

    if ($action === 'finish') {
        $admin = [
            'nama' => trim($_POST['nama'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'pass' => $_POST['password'] ?? '',
            'pass2' => $_POST['password2'] ?? '',
        ];
        if ($admin['nama'] === '') $errors['nama'] = 'Nama wajib diisi.';
        if (strlen($admin['username']) < 4) $errors['username'] = 'Username minimal 4 karakter.';
        if ($admin['email'] !== '' && !filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email tidak valid.';
        if (strlen($admin['pass']) < 6) $errors['password'] = 'Password minimal 6 karakter.';
        if ($admin['pass'] !== $admin['pass2']) $errors['password2'] = 'Konfirmasi password tidak cocok.';

        if (empty($errors)) {
            $result = run_install($_SESSION['install_db'] ?? [], $_SESSION['install_base'] ?? '', $admin);
            if ($result === true) {
                unset($_SESSION['install_db'], $_SESSION['install_base']);
                header('Location: ?step=done');
                exit;
            }
            $errors['general'] = $result;
        }
        $step = 'admin';
        $_SESSION['admin_tmp'] = $admin;
    }
}

// =====================================================================
//  Fungsi-fungsi installer
// =====================================================================
function test_db(array $db): array
{
    $errors = [];
    if ($db['name'] === '' || $db['user'] === '') {
        $errors['general'] = 'Nama database dan username wajib diisi.';
        return $errors;
    }
    try {
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
        new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (Throwable $e) {
        $errors['general'] = 'Gagal koneksi database: ' . $e->getMessage();
    }
    return $errors;
}

function run_sql_file(PDO $pdo, string $file): void
{
    $sql = file_get_contents($file);
    // Hapus komentar baris (-- ...)
    $sql = preg_replace('/^\s*--.*$/m', '', (string) $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if ($stmt !== '') {
            $pdo->exec($stmt);
        }
    }
}

function run_install(array $db, string $base, array $admin): string|bool
{
    try {
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        run_sql_file($pdo, ROOT . '/database/schema.sql');
        run_sql_file($pdo, ROOT . '/database/seed.sql');

        // Buat akun admin pertama
        $stmt = $pdo->prepare('INSERT INTO users (nama, username, email, password, role, is_active) VALUES (?,?,?,?,?,1)');
        $stmt->execute([
            $admin['nama'],
            $admin['username'],
            $admin['email'] !== '' ? $admin['email'] : null,
            password_hash($admin['pass'], PASSWORD_DEFAULT),
            'admin',
        ]);

        // Tulis config
        $baseUrl = $base !== '' ? rtrim($base, '/') . '/' : 'https://tryout.bimbeltemanjuara.com/';
        if (!write_config($db, $baseUrl)) {
            return 'Gagal menulis file config. Pastikan folder /config dapat ditulis (chmod 755/775).';
        }

        // Lock
        file_put_contents(LOCK_FILE, 'Installed at ' . date('c'));
        return true;
    } catch (Throwable $e) {
        return 'Instalasi gagal: ' . $e->getMessage();
    }
}

function write_config(array $db, string $baseUrl): bool
{
    $key = bin2hex(random_bytes(32));
    $tpl = <<<PHP
<?php
/**
 * Konfigurasi TemanJuara - dibuat otomatis oleh installer.
 */
return [
    'app' => [
        'name'     => 'TemanJuara',
        'base_url' => %s,
        'env'      => 'production',
        'key'      => %s,
        'debug'    => false,
    ],
    'db' => [
        'host'    => %s,
        'port'    => %s,
        'name'    => %s,
        'user'    => %s,
        'pass'    => %s,
        'charset' => 'utf8mb4',
    ],
];
PHP;
    $content = sprintf(
        $tpl,
        var_export($baseUrl, true),
        var_export($key, true),
        var_export($db['host'], true),
        var_export((int) $db['port'], true),
        var_export($db['name'], true),
        var_export($db['user'], true),
        var_export($db['pass'], true)
    );
    if (!is_dir(ROOT . '/config')) {
        @mkdir(ROOT . '/config', 0755, true);
    }
    return (bool) @file_put_contents(CONFIG_FILE, $content);
}

// Cek requirement
function requirements(): array
{
    return [
        ['PHP >= 8.2', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION],
        ['Ekstensi PDO MySQL', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'Aktif' : 'Tidak ada'],
        ['Ekstensi mbstring', extension_loaded('mbstring'), extension_loaded('mbstring') ? 'Aktif' : 'Tidak ada'],
        ['Ekstensi GD', extension_loaded('gd'), extension_loaded('gd') ? 'Aktif' : 'Tidak ada'],
        ['Ekstensi fileinfo', extension_loaded('fileinfo'), extension_loaded('fileinfo') ? 'Aktif' : 'Tidak ada'],
        ['Folder /config dapat ditulis', is_writable(ROOT . '/config') || is_writable(ROOT), is_writable(ROOT . '/config') ? 'OK' : 'Perlu chmod'],
    ];
}

// Render halaman sesuai step
render_page($step, $errors);

