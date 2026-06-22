<?php \App\Core\View::extend('layouts.app'); \App\Core\View::start('content');
function attr_json_sub(array $d): string { return e(json_encode($d, JSON_UNESCAPED_UNICODE)); }
?>
<div x-data="{ add:false, edit:{id:null,nama:'',deskripsi:'',urutan:0} }">
    <div class="flex items-center justify-between mb-5">
        <p class="text-slate-500 text-sm max-w-2xl">Sub kemampuan adalah unit terkecil &mdash; di sinilah soal akan dibuat dan narasi penilaian mengacu. Bab: <strong><?= e($bab['nama']) ?></strong>.</p>
        <button @click="add=true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Sub Kemampuan
        </button>
    </div>

    <?php if (empty($sub)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center text-slate-500">Belum ada sub kemampuan.</div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
            <?php foreach ($sub as $s): ?>
            <div class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-slate-800"><?= e($s['nama']) ?></span>
                        <span class="text-[11px] font-mono px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600"><?= e($s['kode']) ?></span>
                        <span class="text-[11px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500"><?= (int) $s['jml_soal'] ?> soal</span>
                    </div>
                    <?php if (!empty($s['deskripsi'])): ?>
                        <p class="text-sm text-slate-500 mt-1"><?= e($s['deskripsi']) ?></p>
                    <?php endif; ?>
                </div>
                <button @click='edit = <?= attr_json_sub(["id"=>(int)$s["id"],"nama"=>$s["nama"],"deskripsi"=>$s["deskripsi"] ?? "","urutan"=>(int)$s["urutan"]]) ?>'
                        class="p-2 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                <form method="post" action="<?= url('admin/pemetaan/sub/' . $s['id'] . '/delete') ?>" onsubmit="return confirm('Hapus sub kemampuan ini?')" class="flex-shrink-0">
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
            <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Sub Kemampuan</h3>
            <form method="post" action="<?= url('admin/pemetaan/sub') ?>" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="bab_id" value="<?= (int) $bab['id'] ?>">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Sub Kemampuan</label>
                    <input name="nama" placeholder="Contoh: Mengidentifikasi Akar" autofocus class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kode (opsional &mdash; otomatis bila kosong)</label>
                    <input name="kode" placeholder="otomatis" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi (opsional)</label>
                    <textarea name="deskripsi" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"></textarea>
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
            <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Sub Kemampuan</h3>
            <form method="post" :action="`<?= url('admin/pemetaan/sub') ?>/${edit.id}/update`" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Sub Kemampuan</label>
                    <input name="nama" x-model="edit.nama" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" x-model="edit.deskripsi" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none text-sm"></textarea>
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
