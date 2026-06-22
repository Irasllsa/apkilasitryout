<?php \App\Core\View::extend('layouts.app'); \App\Core\View::start('content'); ?>
<?php
$cards = [
    ['Draft', $stats['draft'], 'from-slate-500 to-slate-600'],
    ['Sedang Diajukan', $stats['diajukan'], 'from-amber-500 to-amber-600'],
    ['Perlu Revisi', $stats['revisi'], 'from-rose-500 to-rose-600'],
    ['Terpublish', $stats['published'], 'from-emerald-500 to-emerald-600'],
];
?>
<div class="grid grid-cols-2 xl:grid-cols-4 gap-5">
    <?php foreach ($cards as [$label, $value, $grad]): ?>
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500"><?= e($label) ?></span>
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br <?= $grad ?>"></div>
        </div>
        <div class="mt-3 text-3xl font-extrabold text-slate-800"><?= (int) $value ?></div>
    </div>
    <?php endforeach; ?>
</div>
<div class="mt-6 bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800">Halo, <?= e($currentUser['nama']) ?> 👋</h3>
    <p class="text-slate-500 text-sm mt-1">Buat paket soal bertipe <strong>Fokus</strong> (satu sub kemampuan) atau <strong>Ulangan</strong> (beberapa sub kemampuan), lalu ajukan ke admin untuk divalidasi.</p>
    <a href="<?= url('pembuat/paket') ?>" class="inline-block mt-5 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">+ Buat Paket Soal</a>
</div>
<?php \App\Core\View::stop(); ?>
