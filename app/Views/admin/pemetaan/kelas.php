<?php \App\Core\View::extend('layouts.app'); \App\Core\View::start('content'); ?>
<div x-data="{ add:false, edit:null }">
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-slate-500 text-sm">Fondasi kurikulum: <strong>Kelas &rarr; Mata Pelajaran &rarr; Bab &rarr; Sub Kemampuan</strong>. Mulai dari menambah kelas.</p>
        </div>
        <button @click="add=true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Kelas
        </button>
    </div>

    <?php if (empty($kelas)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
            <p class="text-slate-500">Belum ada kelas. Klik <strong>Tambah Kelas</strong> untuk memulai.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($kelas as $k): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition group">
                <div class="flex items-start justify-between">
                    <a href="<?= url('admin/pemetaan/kelas/' . $k['id']) ?>" class="flex items-center gap-3 flex-1">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-800 group-hover:text-indigo-600"><?= e($k['nama']) ?></div>
                            <div class="text-xs text-slate-400"><?= (int) $k['jml_mapel'] ?> mata pelajaran</div>
                        </div>
                    </a>
                    <div x-data="{o:false}" class="relative">
                        <button @click="o=!o" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                        <div x-show="o" @click.outside="o=false" style="display:none" class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-10">
                            <button @click="edit=<?= (int) $k['id'] ?>; o=false" class="w-full text-left px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">Edit</button>
                            <form method="post" action="<?= url('admin/pemetaan/kelas/' . $k['id'] . '/delete') ?>" onsubmit="return confirm('Hapus kelas ini beserta seluruh mapel, bab, dan sub kemampuannya?')">
                                <?= csrf_field() ?>
                                <button class="w-full text-left px-3 py-2 text-sm text-rose-600 hover:bg-rose-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal edit -->
                <div x-show="edit===<?= (int) $k['id'] ?>" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
                    <div @click.outside="edit=null" class="bg-white rounded-2xl w-full max-w-md p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Kelas</h3>
                        <form method="post" action="<?= url('admin/pemetaan/kelas/' . $k['id'] . '/update') ?>" class="space-y-4">
                            <?= csrf_field() ?>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kelas</label>
                                <input name="nama" value="<?= e($k['nama']) ?>" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Urutan</label>
                                <input type="number" name="urutan" value="<?= (int) $k['urutan'] ?>" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="edit=null" class="flex-1 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Batal</button>
                                <button class="flex-1 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal tambah -->
    <div x-show="add" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div @click.outside="add=false" class="bg-white rounded-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Kelas</h3>
            <form method="post" action="<?= url('admin/pemetaan/kelas') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kelas</label>
                    <input name="nama" placeholder="Contoh: Kelas 4" autofocus class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Urutan (opsional)</label>
                    <input type="number" name="urutan" value="0" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="add=false" class="flex-1 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Batal</button>
                    <button class="flex-1 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php \App\Core\View::stop(); ?>
