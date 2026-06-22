-- =====================================================================
--  Data awal (seed) - dijalankan installer setelah schema
-- =====================================================================

-- Default rentang narasi penilaian (dapat diubah Admin di menu Pengaturan Narasi)
INSERT INTO `narasi_rentang` (`label`, `min_nilai`, `max_nilai`, `template`, `urutan`, `is_active`) VALUES
('Sangat Perlu Ditingkatkan', 0.00, 50.00, 'Ananda {nama_siswa} masih sangat perlu meningkatkan pemahaman pada sub materi {sub_kemampuan} (nilai {nilai}).', 1, 1),
('Cukup', 51.00, 70.00, 'Ananda {nama_siswa} cukup memahami sub materi {sub_kemampuan} (nilai {nilai}), namun masih perlu latihan.', 2, 1),
('Baik', 71.00, 85.00, 'Ananda {nama_siswa} sudah memahami dengan BAIK sub materi {sub_kemampuan} (nilai {nilai}).', 3, 1),
('Sangat Baik', 86.00, 100.00, 'Ananda {nama_siswa} sangat menguasai sub materi {sub_kemampuan} (nilai {nilai}). Pertahankan!', 4, 1);

-- Default pengaturan sistem (brand)
INSERT INTO `pengaturan` (`nama_key`, `nilai`) VALUES
('brand_nama', 'Bimbel Teman Juara'),
('brand_logo', ''),
('brand_alamat', ''),
('brand_telp', ''),
('brand_email', ''),
('brand_instagram', ''),
('brand_facebook', ''),
('brand_tiktok', ''),
('brand_youtube', ''),
('wa_template', 'Assalamualaikum, Bapak/Ibu wali dari Ananda {nama_siswa}. Kami informasikan bahwa Ananda memiliki soal *{paket}* yang belum dikerjakan. Mohon untuk diingatkan agar segera mengerjakan. Terima kasih.')
ON DUPLICATE KEY UPDATE `nama_key` = `nama_key`;
