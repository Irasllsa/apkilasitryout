<?php $brand = \App\Core\Settings::get('brand_nama', 'Teman Juara'); ?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk &middot; <?= e($brand) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="h-full bg-slate-50 antialiased">
<div class="min-h-full grid lg:grid-cols-2">

    <!-- Panel kiri (branding) -->
    <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 p-12 text-white relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="relative flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center font-extrabold text-lg">TJ</div>
            <span class="text-lg font-bold"><?= e($brand) ?></span>
        </div>
        <div class="relative">
            <h2 class="text-4xl font-extrabold leading-tight">Belajar Terarah,<br>Juara Bersama.</h2>
            <p class="mt-4 text-indigo-200/80 max-w-md">Platform tryout terstruktur: dari pemetaan kurikulum, bank soal, hingga laporan kemampuan siswa yang mendalam.</p>
        </div>
        <div class="relative text-indigo-200/50 text-sm">&copy; <?= date('Y') ?> <?= e($brand) ?>. Seluruh hak cipta dilindungi.</div>
    </div>

    <!-- Panel kanan (form) -->
    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-sm">
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white flex items-center justify-center font-extrabold">TJ</div>
                <span class="text-lg font-bold text-slate-800"><?= e($brand) ?></span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Selamat datang kembali</h1>
            <p class="text-slate-500 mt-1 mb-6 text-sm">Masuk untuk melanjutkan ke dasbor Anda.</p>

            <?php if (!empty($error)): ?>
                <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('login') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Username atau Email</label>
                    <input type="text" name="username" value="<?= e($old_username ?? '') ?>" autofocus
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition text-sm"
                           placeholder="masukkan username">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition text-sm"
                           placeholder="••••••••">
                </div>
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 active:bg-indigo-800 transition shadow-lg shadow-indigo-600/30">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
