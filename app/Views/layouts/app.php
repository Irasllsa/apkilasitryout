<?php
/** @var array $sections */
$user = $currentUser ?? auth();
$role = $user['role'] ?? 'siswa';
$brand = \App\Core\Settings::get('brand_nama', 'Teman Juara');
$path = (new \App\Core\Request())->path();

/** Definisi menu per role */
$menus = [
    'admin' => [
        ['Dashboard', 'dashboard', 'home'],
        ['Master Pemetaan', 'admin/pemetaan', 'map'],
        ['Data Siswa', 'admin/siswa', 'users'],
        ['Data Pembuat Soal', 'admin/pembuat', 'pencil'],
        ['Validasi Soal', 'admin/validasi', 'check'],
        ['Bank Soal', 'admin/bank-soal', 'book'],
        ['Pengajuan Siswa', 'admin/pengajuan', 'inbox'],
        ['Monitoring Tugas', 'admin/monitoring', 'bell'],
        ['Report On Demand', 'admin/report', 'chart'],
        ['Pengaturan Narasi', 'admin/narasi', 'text'],
        ['Pengaturan Sistem', 'admin/pengaturan', 'cog'],
    ],
    'pembuat' => [
        ['Dashboard', 'dashboard', 'home'],
        ['Buat Paket Soal', 'pembuat/paket', 'pencil'],
        ['Draft Saya', 'pembuat/draft', 'doc'],
        ['Status Pengajuan', 'pembuat/status', 'inbox'],
    ],
    'siswa' => [
        ['Dashboard', 'dashboard', 'home'],
        ['Tugas Soal', 'siswa/tugas', 'doc'],
        ['Riwayat & Report', 'siswa/riwayat', 'chart'],
    ],
];
$menu = $menus[$role] ?? [];

function nav_icon(string $name): string
{
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/>',
        'map' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m6-1a4 4 0 10-4-4 4 4 0 004 4z"/>',
        'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'book' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
        'text' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>',
        'cog' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'doc' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    ];
    return $icons[$name] ?? $icons['doc'];
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Dashboard') ?> &middot; <?= e($brand) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">
<div x-data="{ open: false }" class="min-h-full">

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform lg:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-white/10">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center font-extrabold text-white shadow-lg">TJ</div>
            <div class="leading-tight">
                <div class="text-white font-bold text-sm"><?= e($brand) ?></div>
                <div class="text-[11px] text-slate-400 capitalize"><?= e($role === 'pembuat' ? 'Pembuat Soal' : $role) ?></div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <?php foreach ($menu as [$label, $href, $icon]):
                $active = ($path === '/' . trim($href, '/')) || ($href !== 'dashboard' && str_starts_with($path, '/' . trim($href, '/'))); ?>
                <a href="<?= url($href) ?>"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition <?= $active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-white/5 hover:text-white' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?= nav_icon($icon) ?></svg>
                    <span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-3 border-t border-white/10">
            <form method="post" action="<?= url('logout') ?>">
                <?= csrf_field() ?>
                <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:bg-rose-500/10 hover:text-rose-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div x-show="open" @click="open = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden" style="display:none"></div>

    <!-- Konten -->
    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 h-16 bg-white/80 backdrop-blur border-b border-slate-200 flex items-center justify-between px-4 lg:px-8">
            <div class="flex items-center gap-3">
                <button @click="open = !open" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-base font-semibold text-slate-800 leading-tight"><?= e($title ?? 'Dashboard') ?></h1>
                    <?php if (!empty($breadcrumb)): ?>
                    <nav class="text-xs text-slate-400 flex items-center gap-1.5">
                        <?php foreach ($breadcrumb as $i => $bc): ?>
                            <?php if ($i > 0): ?><span>/</span><?php endif; ?>
                            <?php if (!empty($bc['url'])): ?>
                                <a href="<?= url($bc['url']) ?>" class="hover:text-indigo-600"><?= e($bc['label']) ?></a>
                            <?php else: ?>
                                <span class="text-slate-500"><?= e($bc['label']) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block leading-tight">
                    <div class="text-sm font-semibold text-slate-700"><?= e($user['nama'] ?? '') ?></div>
                    <div class="text-[11px] text-slate-400"><?= e($user['username'] ?? '') ?></div>
                </div>
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 text-white flex items-center justify-center font-semibold text-sm">
                    <?= e(strtoupper(substr($user['nama'] ?? 'U', 0, 1))) ?>
                </div>
            </div>
        </header>

        <main class="p-4 lg:p-8">
            <?php if ($f = flash('success')): ?>
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <?= e($f) ?>
                </div>
            <?php endif; ?>
            <?php if ($f = flash('error')): ?>
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= e($f) ?>
                </div>
            <?php endif; ?>

            <?= $sections['content'] ?? '' ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
