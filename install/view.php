<?php
declare(strict_types=1);

function ie(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function render_shell(string $title, string $body): void
{
    ?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ie($title) ?> &middot; Installer TemanJuara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: {
            brand: { 50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca',900:'#312e81' }
        }, fontFamily: { sans: ['Inter','system-ui','sans-serif'] } } } }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',system-ui,sans-serif}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-2 text-white">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center font-extrabold text-lg shadow-lg">TJ</div>
                <span class="text-xl font-bold tracking-tight">TemanJuara</span>
            </div>
            <p class="text-indigo-200/70 text-sm mt-1">Panduan Instalasi Aplikasi Tryout</p>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="px-8 py-7"><?= $body ?></div>
        </div>
        <p class="text-center text-indigo-200/40 text-xs mt-5">&copy; <?= date('Y') ?> Bimbel Teman Juara</p>
    </div>
</body>
</html><?php
}

function stepper(string $current): string
{
    $steps = ['welcome' => '1. Pengecekan', 'db' => '2. Database', 'admin' => '3. Akun Admin', 'done' => '4. Selesai'];
    $keys = array_keys($steps);
    $currentIdx = array_search($current, $keys, true);
    $out = '<div class="flex items-center justify-between mb-8">';
    foreach ($keys as $i => $k) {
        $done = $i < $currentIdx;
        $active = $k === $current;
        $circle = $active ? 'bg-indigo-600 text-white' : ($done ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500');
        $label = $active ? 'text-indigo-700 font-semibold' : 'text-slate-400';
        $out .= '<div class="flex items-center gap-2">'
            . '<div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold ' . $circle . '">' . ($done ? '&#10003;' : ($i + 1)) . '</div>'
            . '<span class="text-xs ' . $label . ' hidden sm:inline">' . ie(substr($steps[$k], 3)) . '</span></div>';
        if ($i < count($keys) - 1) {
            $out .= '<div class="flex-1 h-px bg-slate-200 mx-2"></div>';
        }
    }
    return $out . '</div>';
}

function alert_error(string $msg): string
{
    return '<div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">' . ie($msg) . '</div>';
}

function render_page(string $step, array $errors): void
{
    $body = stepper($step === 'done' ? 'done' : $step);

    if ($step === 'welcome') {
        $reqs = requirements();
        $allOk = true;
        $rows = '';
        foreach ($reqs as [$label, $ok, $detail]) {
            if (!$ok) $allOk = false;
            $icon = $ok
                ? '<span class="text-emerald-600">&#10003;</span>'
                : '<span class="text-rose-600">&#10007;</span>';
            $rows .= '<div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">'
                . '<span class="text-sm text-slate-700">' . ie($label) . '</span>'
                . '<span class="text-sm font-medium flex items-center gap-2"><span class="text-slate-400 text-xs">' . ie((string) $detail) . '</span>' . $icon . '</span></div>';
        }
        $body .= '<h1 class="text-2xl font-bold text-slate-800">Selamat datang &#128075;</h1>'
            . '<p class="text-slate-500 mt-1 mb-5">Mari kita siapkan aplikasi tryout TemanJuara. Pertama, kami cek kebutuhan server.</p>'
            . '<div class="rounded-xl border border-slate-200 px-4">' . $rows . '</div>';
        if ($allOk) {
            $body .= '<a href="?step=db" class="mt-6 w-full inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Lanjut ke Database &rarr;</a>';
        } else {
            $body .= '<div class="mt-6 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 text-sm">Beberapa kebutuhan belum terpenuhi. Mohon perbaiki dahulu di server Anda.</div>'
                . '<a href="?step=welcome" class="mt-3 w-full inline-flex justify-center px-5 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200">Cek Ulang</a>';
        }
    } elseif ($step === 'db') {
        $db = $_SESSION['install_db'] ?? ['host' => 'localhost', 'port' => '3306', 'name' => 'bimbelt1_tryoutapp', 'user' => 'bimbelt1_tryoutappuser', 'pass' => ''];
        $base = $_SESSION['install_base'] ?? 'https://tryout.bimbeltemanjuara.com/';
        $body .= '<h1 class="text-2xl font-bold text-slate-800">Koneksi Database</h1>'
            . '<p class="text-slate-500 mt-1 mb-5">Masukkan detail database MySQL Anda. Kami akan menguji koneksi.</p>';
        if (!empty($errors['general'])) $body .= alert_error($errors['general']);
        $body .= '<form method="post" class="space-y-4">'
            . '<input type="hidden" name="action" value="db">'
            . '<div class="grid grid-cols-3 gap-3">'
            . field('db_host', 'Host', $db['host'], 'localhost', 'col-span-2')
            . field('db_port', 'Port', $db['port'] ?? '3306', '3306')
            . '</div>'
            . field('db_name', 'Nama Database', $db['name'], 'nama_database')
            . field('db_user', 'Username Database', $db['user'], 'user_database')
            . field('db_pass', 'Password Database', '', '••••••••', '', 'password')
            . field('base_url', 'Base URL Aplikasi', $base, 'https://domain-anda.com/')
            . '<button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Uji Koneksi &amp; Lanjut &rarr;</button>'
            . '</form>';
    } elseif ($step === 'admin') {
        $a = $_SESSION['admin_tmp'] ?? ['nama' => '', 'username' => '', 'email' => ''];
        $body .= '<h1 class="text-2xl font-bold text-slate-800">Buat Akun Admin</h1>'
            . '<p class="text-slate-500 mt-1 mb-5">Akun master/admin pertama untuk mengelola seluruh sistem.</p>';
        if (!empty($errors['general'])) $body .= alert_error($errors['general']);
        $body .= '<form method="post" class="space-y-4">'
            . '<input type="hidden" name="action" value="finish">'
            . field('nama', 'Nama Lengkap', $a['nama'] ?? '', 'Nama Admin', '', 'text', $errors['nama'] ?? null)
            . field('username', 'Username', $a['username'] ?? '', 'admin', '', 'text', $errors['username'] ?? null)
            . field('email', 'Email (opsional)', $a['email'] ?? '', 'admin@email.com', '', 'email', $errors['email'] ?? null)
            . '<div class="grid grid-cols-2 gap-3">'
            . field('password', 'Password', '', '••••••••', '', 'password', $errors['password'] ?? null)
            . field('password2', 'Ulangi Password', '', '••••••••', '', 'password', $errors['password2'] ?? null)
            . '</div>'
            . '<button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Pasang Aplikasi &#9889;</button>'
            . '<a href="?step=db" class="block text-center text-sm text-slate-400 hover:text-slate-600">&larr; Kembali ke database</a>'
            . '</form>';
    } elseif ($step === 'done') {
        $body .= '<div class="text-center py-4">'
            . '<div class="mx-auto w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">'
            . '<svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>'
            . '<h1 class="text-2xl font-bold text-slate-800">Instalasi Berhasil! &#127881;</h1>'
            . '<p class="text-slate-500 mt-2 mb-6">Aplikasi TemanJuara siap digunakan. Demi keamanan, segera hapus folder <code class="bg-slate-100 px-1.5 py-0.5 rounded text-rose-600">/install</code>.</p>'
            . '<a href="../login" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">Masuk ke Aplikasi &rarr;</a>'
            . '</div>';
    }

    render_shell(ucfirst($step), $body);
}

function field(string $name, string $label, string $value, string $placeholder = '', string $colspan = '', string $type = 'text', ?string $error = null): string
{
    $border = $error ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200';
    $out = '<div class="' . $colspan . '">'
        . '<label class="block text-sm font-medium text-slate-700 mb-1.5">' . ie($label) . '</label>'
        . '<input type="' . ie($type) . '" name="' . ie($name) . '" value="' . ie($value) . '" placeholder="' . ie($placeholder) . '" '
        . 'class="w-full px-3.5 py-2.5 rounded-lg border ' . $border . ' focus:ring-4 outline-none transition text-slate-800 text-sm">';
    if ($error) {
        $out .= '<p class="text-rose-600 text-xs mt-1">' . ie($error) . '</p>';
    }
    return $out . '</div>';
}
