<?php \App\Core\View::extend('layouts.app'); \App\Core\View::start('content'); ?>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <a href="<?= url('siswa/tugas') ?>" class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-2xl p-6 text-white hover:-translate-y-0.5 transition">
        <div class="text-sm text-indigo-100 font-medium">Tugas Soal Baru</div>
        <div class="mt-2 text-4xl font-extrabold"><?= (int) $stats['tugas_baru'] ?></div>
        <div class="mt-2 text-sm text-indigo-100">Klik untuk mulai mengerjakan &rarr;</div>
    </a>
    <a href="<?= url('siswa/riwayat') ?>" class="bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-lg transition">
        <div class="text-sm text-slate-500 font-medium">Soal Selesai Dikerjakan</div>
        <div class="mt-2 text-4xl font-extrabold text-slate-800"><?= (int) $stats['selesai'] ?></div>
        <div class="mt-2 text-sm text-slate-400">Lihat riwayat &amp; report harian &rarr;</div>
    </a>
</div>
<div class="mt-6 bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800">Semangat belajar, <?= e($currentUser['nama']) ?>! 🌟</h3>
    <p class="text-slate-500 text-sm mt-1">Kerjakan soal yang diberikan dengan teliti. Setelah selesai, kamu akan langsung mendapat nilai dan laporan kemampuanmu.</p>
</div>
<?php \App\Core\View::stop(); ?>
