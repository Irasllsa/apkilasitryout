<?php $brand = \App\Core\Settings::get('brand_nama', 'Teman Juara'); ?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 &middot; <?= e($brand) ?></title><link rel="stylesheet" href="<?= asset('css/app.css') ?>"></head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
<div class="text-center">
    <div class="text-7xl font-extrabold text-indigo-600">404</div>
    <h1 class="mt-4 text-xl font-bold text-slate-800">Halaman tidak ditemukan</h1>
    <p class="text-slate-500 mt-1">Maaf, halaman yang Anda cari tidak tersedia.</p>
    <a href="<?= url('dashboard') ?>" class="inline-block mt-6 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Kembali ke Dashboard</a>
</div></body></html>
