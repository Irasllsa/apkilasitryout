# Spesifikasi Kebutuhan Aplikasi Tryout — TemanJuara

> Dokumen ini adalah penyusunan ulang dari deskripsi kebutuhan pemilik produk.
> Tujuannya: menjadi acuan komunikasi dengan developer. Typo sudah diperbaiki,
> alur sudah disinkronkan, dan seluruh keputusan kunci sudah difinalkan
> (ditandai dengan **[KEPUTUSAN FINAL]**).

---

## 1. Gambaran Umum

**TemanJuara** adalah aplikasi/web tryout untuk bimbingan belajar. Aplikasi
memfasilitasi pembuatan soal, pengerjaan soal oleh siswa, dan penilaian otomatis
yang menghasilkan laporan (report) kemampuan siswa.

Output paling akhir dari sistem adalah:
1. **Report Harian** — diterima siswa segera setelah mengerjakan satu paket soal
   (tipe **FOKUS** atau **ULANGAN**).
2. **Report On Demand (Bulanan)** — hanya bisa dibuat oleh Admin. Merupakan
   gabungan/rekap dari beberapa report harian dalam rentang waktu tertentu,
   dianalogikan seperti "mutasi rekening bank". Berisi grafik kemampuan, narasi
   kelebihan & kekurangan, rekomendasi, serta identitas brand TemanJuara (logo,
   media sosial, dll).

---

## 2. Peran Pengguna (Roles)

| Peran | Tugas Utama |
|-------|-------------|
| **Master / Admin** | Mengatur seluruh kebutuhan sistem: master data, **Master Pemetaan (termasuk seluruh Sub Kemampuan)**, narasi/penilaian, validasi soal, delegasi soal ke siswa, monitoring, pengaturan brand, dan pembuatan Report On Demand. |
| **Pembuat Soal** | Membuat soal pada Sub Kemampuan yang **sudah disediakan Admin** (tidak membuat/menyusun Sub Kemampuan sendiri), lalu mengajukan paket soal untuk divalidasi Admin. |
| **Siswa** | Mengerjakan soal yang didelegasikan Admin dan menerima report harian. |

---

## 3. Menu Utama (Ringkasan)

**Sisi Admin:**
- Master Data Siswa
- Master Data Pembuat Soal
- Master Pemetaan (fondasi kurikulum & penilaian — termasuk Sub Kemampuan)
- Pengaturan Narasi Penilaian (rentang nilai + template narasi)
- Validasi Soal
- Bank Soal (+ Delegasi Soal)
- Pengajuan Soal dari Siswa
- Siswa yang Sudah Didelegasikan Soal (monitoring follow-up)
- Report On Demand
- Pengaturan Sistem (identitas brand: Nama, Logo, Alamat, No. Telp, media sosial)

**Sisi Pembuat Soal:**
- Daftar Sub Kemampuan yang diberi akses
- Pembuatan Paket Soal (Fokus / Ulangan)
- Draft
- Status Pengajuan (Diajukan / Revisi / ACC)

**Sisi Siswa:**
- Soal yang Didelegasikan (To-do)
- Pengerjaan Soal
- Riwayat & Report Harian
- Pengajuan Kerjakan Ulang ("Soal yang Sama")

---

## 4. Master Pemetaan (Fondasi Sistem)

Master Pemetaan adalah **acuan untuk pembuat soal dan untuk penilaian/narasi report**.
Semua narasi penilaian disusun bersumber dari sini, **sebelum** tahap pembuatan soal.

### 4.1 Struktur Hierarki (berjenjang menurun)

```
Kelas
 └── Mata Pelajaran
      └── Bab
           └── Sub Bab / Sub Kemampuan  ← komponen master paling akhir
                └── (ruang pembuatan soal terbuka di sini)
```

**Contoh:**
- Kelas 4
  - Mata Pelajaran: IPAS
    - Bab: "Bagian Tubuh Tumbuhan dan Fungsinya"
      - Sub Kemampuan: "Mengidentifikasi Akar"
      - Sub Kemampuan: "Mengidentifikasi Daun"
      - Sub Kemampuan: "Mengidentifikasi Bunga"
      - Sub Kemampuan: "Mengidentifikasi Batang"

### 4.2 Aturan Penting
- **Master Pemetaan (termasuk seluruh tingkat: Kelas, Mata Pelajaran, Bab, dan
  Sub Kemampuan) dikelola sepenuhnya oleh Admin.** Pembuat soal tidak dapat
  menambah/mengubah Sub Kemampuan — mereka hanya memilih dari daftar yang sudah
  disediakan Admin.
- **Sub Kemampuan** adalah unit terkecil. Soal hanya bisa dibuat setelah memilih
  sebuah Sub Kemampuan.
- Daftar Sub Kemampuan otomatis muncul sebagai pilihan (dropdown) saat pembuat
  soal membuat soal — **tidak perlu mengetik ulang**, sehingga tidak terjadi
  duplikasi/narasi ganda.
- Setiap penambahan Sub Kemampuan di Master Pemetaan otomatis ter-update pada
  dropdown pembuatan soal.

---

## 5. Sistem Penilaian & Penyusunan Report

### 5.1 Dua Gaya/Tipe Paket Soal
Saat membuat paket soal baru, pembuat soal memilih salah satu tipe:

| Tipe | Karakteristik |
|------|---------------|
| **FOKUS** | Hanya memuat **satu** Sub Kemampuan. Contoh: seluruh soal tentang "Mengidentifikasi Akar". |
| **ULANGAN** | Memuat **beragam** Sub Kemampuan dalam satu paket. Contoh: 4 soal "Akar", 4 soal "Batang", 4 soal "Bunga", 4 soal "Daun". |

### 5.2 Cara Penilaian — **[KEPUTUSAN FINAL]**
- Penilaian menggunakan **skor parsial per opsi/pernyataan** (bukan all-or-nothing).
- **Pilihan Ganda (PG):** 1 soal = 1 kunci. Benar = nilai penuh, salah = 0.
- **Pilihan Ganda Kompleks (PGK):** skor dihitung **per opsi**. Setiap opsi yang
  dijawab sesuai kunci (baik yang seharusnya dipilih maupun yang seharusnya tidak
  dipilih) memberi kontribusi nilai. Nilai soal = (jumlah opsi yang dijawab benar
  ÷ total opsi) dari bobot soal tersebut.
- **Pernyataan Benar/Salah (BS, 4 pernyataan):** skor dihitung **per pernyataan**.
  Tiap pernyataan yang dijawab benar memberi kontribusi (mis. 1 dari 4) terhadap
  bobot soal.
- Nilai akhir paket/sub kemampuan = total skor parsial yang diperoleh ÷ total
  skor maksimum, dinyatakan dalam persentase (0–100).

> Catatan untuk developer: simpan skor mentah per soal/opsi agar fleksibel saat
> agregasi ke level Sub Kemampuan dan Report On Demand.

### 5.3 Narasi Report Berdasarkan Rentang Nilai — **[KEPUTUSAN FINAL]**
Narasi disusun otomatis berdasarkan rentang nilai yang diperoleh siswa per Sub Kemampuan.

Contoh narasi: *"Ananda sudah dapat memahami dengan BAIK sub materi Mengidentifikasi Akar."*

**Keputusan:** Sistem **TIDAK** mengunci rentang & teks narasi secara hardcode.
Sebaliknya, sediakan **pengaturan narasi yang fleksibel & dikelola sendiri oleh
Admin**, dengan kemampuan:
- Menambah/menghapus/mengubah **rentang nilai** (mis. 0–50, 51–70, dst.) sesuai
  kebutuhan.
- Menulis sendiri **teks narasi** untuk tiap rentang.
- Menggunakan **placeholder/variabel dinamis** di dalam teks, contoh:
  `{nama_siswa}`, `{sub_kemampuan}`, `{nilai}` — sehingga satu template narasi
  bisa dipakai untuk banyak siswa & sub kemampuan.

Contoh template yang ditulis Admin:
> "Ananda {nama_siswa} sudah memahami dengan BAIK sub materi {sub_kemampuan} (nilai {nilai})."

> Catatan untuk developer: validasi agar rentang tidak tumpang tindih dan
> mencakup 0–100 tanpa celah.

### 5.4 Narasi untuk Tipe ULANGAN — **[KEPUTUSAN FINAL]**
- Karena satu paket Ulangan memuat beberapa Sub Kemampuan, siswa menerima
  **beberapa narasi** — satu per Sub Kemampuan.
- Nilai dihitung **per Sub Kemampuan** (dipersentasekan dari soal-soal pada sub
  kemampuan tersebut), lalu sistem menonjolkan **mana yang nilainya tinggi** dan
  **mana yang rendah** (perlu ditingkatkan).
- **Keputusan:** Penilaian **tetap dihitung per Sub Kemampuan**, meskipun jumlah
  soal antar Sub Kemampuan tidak sama. Persentase tiap Sub Kemampuan dihitung
  relatif terhadap jumlah soal pada Sub Kemampuan itu sendiri (mis. Sub A: 3/4 =
  75%, Sub B: 1/2 = 50%).

### 5.5 Prinsip
Semua narasi (report harian & on demand) bersumber dari Master Pemetaan yang
disusun **sebelum** tahap pembuatan soal.

---

## 6. Tahap Pembuatan Soal (Sisi Pembuat Soal)

Fitur yang tersedia pada layar pembuatan soal:

1. **Dropdown Sub Kemampuan** — sumber dari Master Pemetaan. Menentukan soal ini
   masuk ke sub kemampuan mana.
2. **Upload gambar** — pada batang soal maupun pada opsi jawaban.
3. **Impor soal dari Excel** — tersedia template baku yang mudah dibaca mesin
   agar soal terunggah otomatis. (Rekomendasi format ada di Lampiran A.)
4. **Kategori jenis soal:**
   - Pilihan Ganda (PG)
   - Pilihan Ganda Kompleks (jawaban benar bisa > 1)
   - Pernyataan Benar/Salah (umumnya 4 pernyataan; siswa memilih benar/salah tiap pernyataan)
5. Pembuat soal bebas membuat **berapa pun** jumlah soal dalam satu paket.
6. Setiap soal **wajib** terhubung ke "Sub Kemampuan" (dari Master Pemetaan).
7. **Draft** — menyimpan paket soal yang belum selesai untuk dilanjutkan nanti.
8. **Upload pembahasan** — file Word/PDF sebagai pembahasan manual.
9. **Kunci jawaban** — pembuat soal wajib menyertakan kunci jawaban tiap soal,
   agar penilaian dapat berjalan **real-time/otomatis**.
10. **Ajukan ke Admin** — setelah satu paket selesai, pembuat soal mengajukan
    paket untuk divalidasi; paket muncul di menu **Validasi Soal** milik Admin.

---

## 7. Menu Validasi Soal (Sisi Admin)

Berisi seluruh paket soal yang diajukan pembuat soal. Admin memeriksa:
- Susunan soal beserta kunci jawaban.
- File pembahasan manual yang diunggah.
- Kesesuaian soal dengan Sub Kemampuan yang dipilih, dan pengecekan lain.

Aksi Admin:
1. **ACC** → paket dipindahkan ke **Bank Soal** (status: TERPUBLISH).
2. **Revisi** → status pengajuan pada akun pembuat soal berubah dari
   "Soal Sedang Diajukan" menjadi "Revisi".
3. Saat Admin klik **Revisi**, paket tersebut hilang dari dashboard Validasi
   Admin (dikembalikan ke pembuat soal untuk diperbaiki).

**[KEPUTUSAN FINAL]** Saat menolak/merevisi, Admin **wajib mengisi kolom
catatan/alasan revisi**. Catatan ini ditampilkan ke pembuat soal sebagai
panduan perbaikan, dan disimpan pada riwayat paket soal.

---

## 8. Menu Bank Soal (Sisi Admin)

1. Berisi seluruh paket soal yang sudah ter-**ACC**. Tersusun berdasarkan:
   **Kelas → Mata Pelajaran → Bab**.
2. Di sinilah tahap **Delegasi Soal** ke siswa dilakukan.
3. Admin mendelegasikan paket soal ke siswa yang dituju.
4. Setelah didelegasikan, siswa menerima paket dan dapat mengerjakannya
   **sesuai waktu yang diatur Admin**.

**[KEPUTUSAN FINAL]**
- **Delegasi ditujukan ke siswa per individu** (Admin memilih siswa satu per satu).
- **Pengaturan waktu:** ada **waktu mulai/buka** dan **waktu deadline/tutup**.
  Siswa hanya dapat mengerjakan dalam rentang tersebut.
- **Urutan soal tidak diacak** pada delegasi awal (soal tampil sesuai urutan
  yang disusun pembuat soal).

---

## 9. POV Siswa

1. Siswa menerima soal yang didelegasikan Admin.
2. Setelah selesai mengerjakan, langsung keluar **nilai + narasi Report Harian**
   berdasarkan hasil pengerjaan.
3. Soal yang sudah dikerjakan **tidak dapat dikerjakan lagi**, namun siswa dapat
   melihat **riwayat** (mana yang benar/salah) — yaitu report hariannya.
4. Siswa dapat **meminta "soal yang sama"** ke Admin untuk dikirimi ulang paket
   yang sama agar bisa dikerjakan kembali.

**[KEPUTUSAN FINAL]**
- Saat pengerjaan ulang ("soal yang sama"), **urutan soal diacak** (soal tetap
  dari paket yang sama, namun urutannya diacak agar tidak hafalan posisi).
- Setiap pengerjaan ulang dicatat sebagai **attempt/riwayat baru** (tidak menimpa
  yang lama), sehingga seluruh percobaan terekam pada riwayat siswa.
  > Catatan untuk developer: pada Report On Demand, **semua attempt ditampilkan**
  > pada rekap riwayat siswa, namun untuk perhitungan/agregasi nilai gunakan
  > **default "nilai terbaik" (best attempt)**.
- **Tampilan pembahasan & kunci jawaban ke siswa bersifat fleksibel:** Admin
  dapat mengatur (per delegasi/paket) apakah siswa boleh melihat pembahasan dan
  pembahasan benar/salah pada halaman riwayat, atau tidak.

---

## 10. Menu Pengajuan Soal dari Siswa (Sisi Admin)

- Menampung semua siswa yang mengajukan untuk mengerjakan ulang soal yang sudah
  dikerjakan.
- Admin dapat memberikan kembali paket soal yang sama untuk dikerjakan ulang,
  dengan jangka waktu tertentu.

---

## 11. Menu Siswa yang Sudah Didelegasikan Soal (Monitoring / Follow-up)

- Menampilkan **hanya** siswa yang sudah didelegasikan soal **tetapi belum
  mengerjakan**.
- Berguna untuk follow-up: Admin dapat menghubungi wali siswa via WhatsApp agar
  siswa segera mengerjakan.
- Saat siswa **selesai mengerjakan**, namanya **otomatis hilang** dari daftar ini
  (agar tidak menumpuk). → **Ya, ini bisa dilakukan secara otomatis** oleh sistem
  berdasarkan status pengerjaan.

**[KEPUTUSAN FINAL]** Integrasi WhatsApp menggunakan **tombol Klik-to-WA**:
sistem membuka WhatsApp dengan **nomor wali siswa terisi otomatis** dan
**template pesan** yang sudah disiapkan. Tidak menggunakan pengiriman otomatis
via WhatsApp Business API.

---

## 12. Menu Report On Demand (Sisi Admin)

- Disusun berdasarkan riwayat pada akun masing-masing siswa.
- Alur: Admin memilih kategori (mis. **Kelas 6**) → muncul daftar nama seluruh
  siswa Kelas 6 → Admin dapat **men-download** rekap riwayat pengerjaan tiap
  siswa untuk dijadikan **lembar Report Bulanan**.
- Isi Report Bulanan:
  - Daftar paket apa saja yang dikerjakan dalam rentang waktu tertentu (dari–sampai).
  - **Grafik kemampuan** siswa.
  - **Narasi evaluasi** (kelebihan & kekurangan).
  - **Rekomendasi**.
  - **Penanganan attempt ganda:** seluruh attempt pengerjaan **ditampilkan** pada
    rekap riwayat, namun perhitungan grafik & nilai menggunakan **default "nilai
    terbaik" (best attempt)**.
  - **Identitas brand** TemanJuara: **Nama, Logo, Alamat, No. Telepon**, dan
    media sosial — diambil dari **Pengaturan Sistem** yang dapat Admin sesuaikan.

**[KEPUTUSAN FINAL]**
- Format download: **PDF** (lembar report siap cetak).
- Header/identitas pada PDF (Nama, Logo, Alamat, No. Telepon, media sosial)
  **bersumber dari menu Pengaturan Sistem** sehingga mudah diubah Admin tanpa
  mengubah kode.

---

## 13. Menu Pengaturan Sistem (Sisi Admin)

Tempat Admin mengelola data global yang dipakai di seluruh aplikasi:
- **Identitas brand:** Nama lembaga, Logo, Alamat, No. Telepon, akun media sosial
  — dipakai pada header/footer Report On Demand (PDF) dan tampilan aplikasi.
- Data ini dapat diubah sewaktu-waktu tanpa mengubah kode.

---

## Ringkasan Keputusan Final (Acuan Cepat Developer)

| No | Topik | Keputusan |
|----|-------|-----------|
| 1 | Metode skor | **Parsial per opsi/pernyataan** (PGK & BS dihitung per opsi/pernyataan; PG benar/salah penuh). |
| 2 | Narasi rentang nilai | Sistem **fleksibel**: Admin membuat sendiri rentang & teks narasi, mendukung placeholder dinamis. |
| 3 | Penilaian Ulangan | **Per Sub Kemampuan**, meski jumlah soal antar sub tidak sama. |
| 4 | Revisi soal | Admin **wajib isi alasan revisi**; ditampilkan ke pembuat soal. |
| 5 | Delegasi | **Per individu siswa**; ada **waktu mulai + deadline**; **soal tidak diacak** saat delegasi awal. |
| 6 | Kerjakan ulang | **Soal diacak**; dicatat sebagai **attempt baru**. Pada Report On Demand **semua attempt ditampilkan**, agregasi nilai pakai **default "nilai terbaik"**. |
| 7 | Lihat pembahasan | **Fleksibel** — Admin atur apakah siswa boleh lihat pembahasan/kunci. |
| 8 | WhatsApp follow-up | **Klik-to-WA** (nomor wali + template pesan otomatis). |
| 9 | Report On Demand | **PDF**; identitas brand (Nama/Logo/Alamat/No.Telp) dari **Pengaturan Sistem**. |
| 10 | Impor Excel | **Disetujui**: template `.xlsx` + validasi + kode unik sub kemampuan. |
| — | Master Pemetaan | **Sub Kemampuan dikelola Admin**, bukan pembuat soal. |

---

## Lampiran A — Rekomendasi Template Impor Soal dari Excel

Agar mudah dibaca mesin, gunakan **satu baris = satu soal**, dengan kolom baku.
Rekomendasi kolom:

| Kolom | Keterangan | Contoh |
|-------|------------|--------|
| `no` | Nomor urut | 1 |
| `tipe_soal` | `PG` / `PGK` / `BS` | PG |
| `sub_kemampuan` | Harus cocok persis dengan nama di Master Pemetaan (atau pakai kode) | Mengidentifikasi Akar |
| `pertanyaan` | Teks soal | Bagian tumbuhan yang menyerap air adalah... |
| `opsi_a` ... `opsi_e` | Teks tiap opsi (BS: isi pernyataan 1–4) | Akar / Daun / ... |
| `kunci` | PG: huruf (A). PGK: banyak huruf (A,C). BS: B/S per pernyataan (B,S,B,S) | A |
| `gambar_soal` | Nama file/URL gambar (opsional) | akar.png |
| `pembahasan` | Teks pembahasan singkat (opsional) | ... |

Rekomendasi teknis untuk developer (**disetujui pemilik produk**):
- Sediakan **template `.xlsx` siap pakai** dengan dropdown (data validation) pada
  kolom `tipe_soal` dan `sub_kemampuan` agar input konsisten.
- **Validasi saat impor**: tolak baris jika `sub_kemampuan` tidak ada di Master
  Pemetaan, atau format `kunci` tidak sesuai `tipe_soal`. Tampilkan ringkasan
  error per baris.
- Gunakan **kode unik** untuk sub kemampuan (mis. `K4-IPAS-B1-S1`) agar lebih
  tahan terhadap perbedaan ejaan dibanding mencocokkan teks.

---

## Lampiran B — Catatan Sinkronisasi Alur (Status Lifecycle)

**Lifecycle Paket Soal:**
```
Draft (pembuat soal)
  → Diajukan (muncul di Validasi Soal Admin)
      → Revisi (kembali ke pembuat soal) ──┐
      → ACC → Terpublish di Bank Soal       │
                → Didelegasikan ke Siswa     │
                                              └─(perbaiki & ajukan ulang)
```

**Lifecycle Pengerjaan Siswa:**
```
Didelegasikan (belum dikerjakan) → muncul di menu Monitoring Follow-up
  → Dikerjakan → Report Harian terbit → hilang dari menu Monitoring
      → (opsi) Ajukan "Soal yang Sama" → Admin setujui → didelegasikan ulang
```
