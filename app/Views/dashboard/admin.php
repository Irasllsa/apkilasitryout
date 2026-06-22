<?php \App\Core\View::extend('layouts.app'); \App\Core\View::start('content'); ?>
<?php
$cards = [
    ['Total Siswa', $stats['siswa'], 'from-indigo-500 to-indigo-600', 'admin/siswa'],
    ['Pembuat Soal', $stats['pembuat'], 'from-violet-500 to-violet-600', 'admin/pembuat'],
    ['Soal Terpublish', $stats['paket_published'], 'from-emerald-500 to-emerald-600', 'admin/bank-soal'],
    ['Perlu Divalidasi', $stats['perlu_validasi'], 'from-amber-500 to-amber-600', 'admin/validasi'],
];
?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
    <?php foreach ($cards as [$label, $value, $grad, $href]): ?>
    <a href="<?= url($href) ?>" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500"><?= e($label) ?></span>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?= $grad ?> opacity-90 group-hover:opacity-100"></div>
        </div>
        <div class="mt-3 text-3xl font-extrabold text-slate-800"><?= (int) $value ?></div>
    </a>
    <?php endforeach; ?>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800">Selamat datang, <?= e($currentUser['nama']) ?> 👋</h3>
        <p class="text-slate-500 text-sm mt-1">Kelola seluruh ekosistem TemanJuara dari sini. Mulai dengan menyiapkan <strong>Master Pemetaan</strong> sebagai fondasi kurikulum dan penilaian.</p>
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="<?= url('admin/pemetaan') ?>" class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Buka Master Pemetaan</a>
            <a href="<?= url('admin/siswa') ?>" class="px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Kelola Siswa</a>
        </div>
    </div>
    <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-2xl p-6 text-white">
        <h3 class="font-semibold">Alur Sistem</h3>
        <ol class="mt-3 space-y-2 text-sm text-indigo-100">
            <li>1. Susun Master Pemetaan</li>
            <li>2. Pembuat soal membuat paket</li>
            <li>3. Validasi &amp; publish ke Bank Soal</li>
            <li>4. Delegasi ke siswa</li>
            <li>5. Siswa kerjakan &amp; terima report</li>
        </ol>
    </div>
</div>
<?php \App\Core\View::stop(); ?>
