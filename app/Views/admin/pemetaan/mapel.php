<?php \App\Core\View::extend('layouts.app'); \App\Core\View::start('content');
function attr_json(array $d): string { return e(json_encode($d, JSON_UNESCAPED_UNICODE)); }
?>
<div x-data="{ add:false, edit:{id:null,nama:'',urutan:0} }">
    <div class="flex items-center justify-between mb-5">
        <p class="text-slate-500 text-sm">Mata pelajaran untuk <strong><?= e($kelas['nama']) ?></strong>. Klik salah satu untuk mengelola bab.</p>
        <button @click="add=true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Mapel
        </button>
    </div>

    <?php if (empty($mapel)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center text-slate-500">Belum ada mata pelajaran.</div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
            <?php foreach ($mapel as $m): ?>
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50">
                <a href="<?= url('admin/pemetaan/mapel/' . $m['id']) ?>" class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center font-bold flex-shrink-0"><?= e(strtoupper(substr($m['nama'],0,1))) ?></div>
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-800 truncate"><?= e($m['nama']) ?></div>
                        <div class="text-xs text-slate-400"><?= (int) $m['jml_bab'] ?> bab</div>
                    </div>
                </a>
                <button @click='edit = <?= attr_json(["id"=>(int)$m["id"],"nama"=>$m["nama"],"urutan"=>(int)$m["urutan"]]) ?>'
                        class="p-2 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                <form method="post" action="<?= url('admin/pemetaan/mapel/' . $m['id'] . '/delete') ?>" onsubmit="return confirm('Hapus mata pelajaran ini beserta isinya?')">
                    <?= csrf_field() ?>
                    <button class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal tambah -->
    <div x-show="add" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div @click.outside="add=false" class="bg-white rounded-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Mata Pelajaran</h3>
            <form method="post" action="<?= url('admin/pemetaan/mapel') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="kelas_id" value="<?= (int) $kelas['id'] ?>">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Mata Pelajaran</label>
                    <input name="nama" placeholder="Contoh: IPAS" autofocus class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Urutan</label>
                    <input type="number" name="urutan" value="0" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="add=false" class="flex-1 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Batal</button>
                    <button class="flex-1 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal edit -->
    <div x-show="edit.id" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div @click.outside="edit.id=null" class="bg-white rounded-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Mata Pelajaran</h3>
            <form method="post" :action="`<?= url('admin/pemetaan/mapel') ?>/${edit.id}/update`" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Mata Pelajaran</label>
                    <input name="nama" x-model="edit.nama" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Urutan</label>
                    <input type="number" name="urutan" x-model="edit.urutan" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="edit.id=null" class="flex-1 px-4 py-2.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Batal</button>
                    <button class="flex-1 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php \App\Core\View::stop(); ?>
