<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

final class PemetaanController extends Controller
{
    // ---------------- KELAS ----------------
    public function index(): string
    {
        $kelas = Database::all(
            'SELECT k.*, 
                (SELECT COUNT(*) FROM mata_pelajaran m WHERE m.kelas_id = k.id) AS jml_mapel
             FROM kelas k ORDER BY k.urutan, k.nama'
        );
        return $this->view('admin.pemetaan.kelas', [
            'title' => 'Master Pemetaan',
            'breadcrumb' => [['label' => 'Master Pemetaan']],
            'kelas' => $kelas,
        ]);
    }

    public function storeKelas(): never
    {
        $nama = trim((string) input('nama'));
        if ($nama === '') {
            Session::flash('error', 'Nama kelas wajib diisi.');
            $this->redirect('admin/pemetaan');
        }
        $urutan = (int) input('urutan', 0);
        Database::insert('INSERT INTO kelas (nama, urutan) VALUES (?, ?)', [$nama, $urutan]);
        Session::flash('success', 'Kelas "' . $nama . '" ditambahkan.');
        $this->redirect('admin/pemetaan');
    }

    public function updateKelas(string $id): never
    {
        $nama = trim((string) input('nama'));
        Database::run('UPDATE kelas SET nama = ?, urutan = ? WHERE id = ?', [$nama, (int) input('urutan', 0), (int) $id]);
        Session::flash('success', 'Kelas diperbarui.');
        $this->redirect('admin/pemetaan');
    }

    public function deleteKelas(string $id): never
    {
        Database::run('DELETE FROM kelas WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Kelas dihapus beserta seluruh isinya.');
        $this->redirect('admin/pemetaan');
    }

    // ---------------- MATA PELAJARAN ----------------
    public function showKelas(string $id): string
    {
        $kelas = Database::first('SELECT * FROM kelas WHERE id = ?', [(int) $id]);
        if ($kelas === null) {
            $this->redirect('admin/pemetaan');
        }
        $mapel = Database::all(
            'SELECT m.*, (SELECT COUNT(*) FROM bab b WHERE b.mapel_id = m.id) AS jml_bab
             FROM mata_pelajaran m WHERE m.kelas_id = ? ORDER BY m.urutan, m.nama',
            [(int) $id]
        );
        return $this->view('admin.pemetaan.mapel', [
            'title' => 'Mata Pelajaran - ' . $kelas['nama'],
            'breadcrumb' => [
                ['label' => 'Master Pemetaan', 'url' => 'admin/pemetaan'],
                ['label' => $kelas['nama']],
            ],
            'kelas' => $kelas,
            'mapel' => $mapel,
        ]);
    }

    public function storeMapel(): never
    {
        $kelasId = (int) input('kelas_id');
        $nama = trim((string) input('nama'));
        if ($nama === '') {
            Session::flash('error', 'Nama mata pelajaran wajib diisi.');
            $this->redirect('admin/pemetaan/kelas/' . $kelasId);
        }
        Database::insert('INSERT INTO mata_pelajaran (kelas_id, nama, urutan) VALUES (?, ?, ?)', [$kelasId, $nama, (int) input('urutan', 0)]);
        Session::flash('success', 'Mata pelajaran ditambahkan.');
        $this->redirect('admin/pemetaan/kelas/' . $kelasId);
    }

    public function updateMapel(string $id): never
    {
        $nama = trim((string) input('nama'));
        Database::run('UPDATE mata_pelajaran SET nama = ?, urutan = ? WHERE id = ?', [$nama, (int) input('urutan', 0), (int) $id]);
        $kelasId = (int) Database::scalar('SELECT kelas_id FROM mata_pelajaran WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Mata pelajaran diperbarui.');
        $this->redirect('admin/pemetaan/kelas/' . $kelasId);
    }

    public function deleteMapel(string $id): never
    {
        $kelasId = (int) Database::scalar('SELECT kelas_id FROM mata_pelajaran WHERE id = ?', [(int) $id]);
        Database::run('DELETE FROM mata_pelajaran WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Mata pelajaran dihapus.');
        $this->redirect('admin/pemetaan/kelas/' . $kelasId);
    }

    // ---------------- BAB ----------------
    public function showMapel(string $id): string
    {
        $mapel = Database::first(
            'SELECT m.*, k.nama AS kelas_nama, k.id AS kelas_id 
             FROM mata_pelajaran m JOIN kelas k ON k.id = m.kelas_id WHERE m.id = ?',
            [(int) $id]
        );
        if ($mapel === null) {
            $this->redirect('admin/pemetaan');
        }
        $bab = Database::all(
            'SELECT b.*, (SELECT COUNT(*) FROM sub_kemampuan s WHERE s.bab_id = b.id) AS jml_sub
             FROM bab b WHERE b.mapel_id = ? ORDER BY b.urutan, b.nama',
            [(int) $id]
        );
        return $this->view('admin.pemetaan.bab', [
            'title' => 'Bab - ' . $mapel['nama'],
            'breadcrumb' => [
                ['label' => 'Master Pemetaan', 'url' => 'admin/pemetaan'],
                ['label' => $mapel['kelas_nama'], 'url' => 'admin/pemetaan/kelas/' . $mapel['kelas_id']],
                ['label' => $mapel['nama']],
            ],
            'mapel' => $mapel,
            'bab' => $bab,
        ]);
    }

    public function storeBab(): never
    {
        $mapelId = (int) input('mapel_id');
        $nama = trim((string) input('nama'));
        if ($nama === '') {
            Session::flash('error', 'Nama bab wajib diisi.');
            $this->redirect('admin/pemetaan/mapel/' . $mapelId);
        }
        Database::insert('INSERT INTO bab (mapel_id, nama, urutan) VALUES (?, ?, ?)', [$mapelId, $nama, (int) input('urutan', 0)]);
        Session::flash('success', 'Bab ditambahkan.');
        $this->redirect('admin/pemetaan/mapel/' . $mapelId);
    }

    public function updateBab(string $id): never
    {
        $nama = trim((string) input('nama'));
        Database::run('UPDATE bab SET nama = ?, urutan = ? WHERE id = ?', [$nama, (int) input('urutan', 0), (int) $id]);
        $mapelId = (int) Database::scalar('SELECT mapel_id FROM bab WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Bab diperbarui.');
        $this->redirect('admin/pemetaan/mapel/' . $mapelId);
    }

    public function deleteBab(string $id): never
    {
        $mapelId = (int) Database::scalar('SELECT mapel_id FROM bab WHERE id = ?', [(int) $id]);
        Database::run('DELETE FROM bab WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Bab dihapus.');
        $this->redirect('admin/pemetaan/mapel/' . $mapelId);
    }

    // ---------------- SUB KEMAMPUAN ----------------
    public function showBab(string $id): string
    {
        $bab = Database::first(
            'SELECT b.*, m.nama AS mapel_nama, m.id AS mapel_id, k.nama AS kelas_nama, k.id AS kelas_id
             FROM bab b JOIN mata_pelajaran m ON m.id = b.mapel_id JOIN kelas k ON k.id = m.kelas_id
             WHERE b.id = ?',
            [(int) $id]
        );
        if ($bab === null) {
            $this->redirect('admin/pemetaan');
        }
        $sub = Database::all(
            'SELECT s.*, (SELECT COUNT(*) FROM soal so WHERE so.sub_kemampuan_id = s.id) AS jml_soal
             FROM sub_kemampuan s WHERE s.bab_id = ? ORDER BY s.urutan, s.nama',
            [(int) $id]
        );
        return $this->view('admin.pemetaan.sub', [
            'title' => 'Sub Kemampuan - ' . $bab['nama'],
            'breadcrumb' => [
                ['label' => 'Master Pemetaan', 'url' => 'admin/pemetaan'],
                ['label' => $bab['kelas_nama'], 'url' => 'admin/pemetaan/kelas/' . $bab['kelas_id']],
                ['label' => $bab['mapel_nama'], 'url' => 'admin/pemetaan/mapel/' . $bab['mapel_id']],
                ['label' => $bab['nama']],
            ],
            'bab' => $bab,
            'sub' => $sub,
        ]);
    }

    public function storeSub(): never
    {
        $babId = (int) input('bab_id');
        $nama = trim((string) input('nama'));
        if ($nama === '') {
            Session::flash('error', 'Nama sub kemampuan wajib diisi.');
            $this->redirect('admin/pemetaan/bab/' . $babId);
        }
        $kode = trim((string) input('kode'));
        if ($kode === '') {
            $kode = $this->generateKode($babId);
        }
        if (Database::scalar('SELECT COUNT(*) FROM sub_kemampuan WHERE kode = ?', [$kode]) > 0) {
            $kode .= '-' . substr(uniqid(), -3);
        }
        Database::insert(
            'INSERT INTO sub_kemampuan (bab_id, kode, nama, deskripsi, urutan) VALUES (?, ?, ?, ?, ?)',
            [$babId, $kode, $nama, trim((string) input('deskripsi')) ?: null, (int) input('urutan', 0)]
        );
        Session::flash('success', 'Sub kemampuan "' . $nama . '" ditambahkan (kode: ' . $kode . ').');
        $this->redirect('admin/pemetaan/bab/' . $babId);
    }

    public function updateSub(string $id): never
    {
        $nama = trim((string) input('nama'));
        Database::run(
            'UPDATE sub_kemampuan SET nama = ?, deskripsi = ?, urutan = ? WHERE id = ?',
            [$nama, trim((string) input('deskripsi')) ?: null, (int) input('urutan', 0), (int) $id]
        );
        $babId = (int) Database::scalar('SELECT bab_id FROM sub_kemampuan WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Sub kemampuan diperbarui.');
        $this->redirect('admin/pemetaan/bab/' . $babId);
    }

    public function deleteSub(string $id): never
    {
        $babId = (int) Database::scalar('SELECT bab_id FROM sub_kemampuan WHERE id = ?', [(int) $id]);
        Database::run('DELETE FROM sub_kemampuan WHERE id = ?', [(int) $id]);
        Session::flash('success', 'Sub kemampuan dihapus.');
        $this->redirect('admin/pemetaan/bab/' . $babId);
    }

    /**
     * Buat kode unik otomatis: K{kelas}-{MAPEL}-B{bab}-S{n}
     */
    private function generateKode(int $babId): string
    {
        $info = Database::first(
            'SELECT k.urutan AS kel_urut, k.nama AS kel_nama, m.nama AS mapel_nama, b.urutan AS bab_urut
             FROM bab b JOIN mata_pelajaran m ON m.id = b.mapel_id JOIN kelas k ON k.id = m.kelas_id
             WHERE b.id = ?',
            [$babId]
        );
        $kel = $info ? preg_replace('/[^0-9A-Za-z]/', '', $info['kel_nama']) : 'K';
        $mapel = $info ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $info['mapel_nama']), 0, 4)) : 'MP';
        $babUrut = $info ? ((int) $info['bab_urut'] ?: $babId) : $babId;
        $n = (int) Database::scalar('SELECT COUNT(*) FROM sub_kemampuan WHERE bab_id = ?', [$babId]) + 1;
        return strtoupper($kel) . '-' . $mapel . '-B' . $babUrut . '-S' . $n;
    }
}
