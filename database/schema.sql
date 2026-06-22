-- =====================================================================
--  TemanJuara Tryout - Skema Database
--  MySQL / MariaDB - InnoDB - utf8mb4
-- =====================================================================
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- Bersihkan tabel lama (jika ada) agar instalasi selalu dari kondisi
-- bersih. Mencegah error foreign key (errno 150) akibat tabel sisa
-- dari percobaan instalasi sebelumnya atau aplikasi lain di DB ini.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `attempt_subskor`;
DROP TABLE IF EXISTS `attempt_jawaban`;
DROP TABLE IF EXISTS `attempt`;
DROP TABLE IF EXISTS `delegasi`;
DROP TABLE IF EXISTS `pengajuan_ulang`;
DROP TABLE IF EXISTS `soal_opsi`;
DROP TABLE IF EXISTS `soal`;
DROP TABLE IF EXISTS `paket_soal`;
DROP TABLE IF EXISTS `narasi_rentang`;
DROP TABLE IF EXISTS `sub_kemampuan`;
DROP TABLE IF EXISTS `bab`;
DROP TABLE IF EXISTS `mata_pelajaran`;
DROP TABLE IF EXISTS `kelas`;
DROP TABLE IF EXISTS `siswa_detail`;
DROP TABLE IF EXISTS `pengaturan`;
DROP TABLE IF EXISTS `users`;

-- ---------------------------------------------------------------------
-- USERS (Admin / Pembuat Soal / Siswa)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`          VARCHAR(150) NOT NULL,
  `username`      VARCHAR(60)  NOT NULL,
  `email`         VARCHAR(150) DEFAULT NULL,
  `password`      VARCHAR(255) NOT NULL,
  `role`          ENUM('admin','pembuat','siswa') NOT NULL DEFAULT 'siswa',
  `no_hp`         VARCHAR(30)  DEFAULT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_by`    BIGINT UNSIGNED DEFAULT NULL,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- DETAIL SISWA (data tambahan untuk role siswa)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `siswa_detail` (
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `kelas_id`    BIGINT UNSIGNED DEFAULT NULL,
  `nis`         VARCHAR(50) DEFAULT NULL,
  `nama_wali`   VARCHAR(150) DEFAULT NULL,
  `hp_wali`     VARCHAR(30)  DEFAULT NULL,
  `asal_sekolah` VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `idx_siswa_kelas` (`kelas_id`),
  CONSTRAINT `fk_siswa_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  MASTER PEMETAAN (dikelola Admin)
--  Kelas > Mata Pelajaran > Bab > Sub Kemampuan
-- =====================================================================
CREATE TABLE IF NOT EXISTS `kelas` (
  `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`      VARCHAR(100) NOT NULL,
  `urutan`    INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kelas_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mata_pelajaran` (
  `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kelas_id`  BIGINT UNSIGNED NOT NULL,
  `nama`      VARCHAR(150) NOT NULL,
  `urutan`    INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mapel_kelas` (`kelas_id`),
  CONSTRAINT `fk_mapel_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bab` (
  `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mapel_id`  BIGINT UNSIGNED NOT NULL,
  `nama`      VARCHAR(200) NOT NULL,
  `urutan`    INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bab_mapel` (`mapel_id`),
  CONSTRAINT `fk_bab_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_kemampuan` (
  `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bab_id`    BIGINT UNSIGNED NOT NULL,
  `kode`      VARCHAR(60) NOT NULL,
  `nama`      VARCHAR(255) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `urutan`    INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sub_kode` (`kode`),
  KEY `idx_sub_bab` (`bab_id`),
  CONSTRAINT `fk_sub_bab` FOREIGN KEY (`bab_id`) REFERENCES `bab`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  PAKET SOAL & SOAL
-- =====================================================================
CREATE TABLE IF NOT EXISTS `paket_soal` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul`           VARCHAR(200) NOT NULL,
  `tipe`            ENUM('fokus','ulangan') NOT NULL,
  `kelas_id`        BIGINT UNSIGNED DEFAULT NULL,
  `mapel_id`        BIGINT UNSIGNED DEFAULT NULL,
  `deskripsi`       TEXT DEFAULT NULL,
  `pembahasan_file` VARCHAR(255) DEFAULT NULL,
  `status`          ENUM('draft','diajukan','revisi','published') NOT NULL DEFAULT 'draft',
  `catatan_revisi`  TEXT DEFAULT NULL,
  `created_by`      BIGINT UNSIGNED NOT NULL,
  `validated_by`    BIGINT UNSIGNED DEFAULT NULL,
  `submitted_at`    DATETIME DEFAULT NULL,
  `validated_at`    DATETIME DEFAULT NULL,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_paket_status` (`status`),
  KEY `idx_paket_creator` (`created_by`),
  KEY `idx_paket_kelas_mapel` (`kelas_id`,`mapel_id`),
  CONSTRAINT `fk_paket_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `soal` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `paket_id`         BIGINT UNSIGNED NOT NULL,
  `sub_kemampuan_id` BIGINT UNSIGNED NOT NULL,
  `tipe`             ENUM('pg','pgk','bs') NOT NULL DEFAULT 'pg',
  `pertanyaan`       TEXT NOT NULL,
  `gambar`           VARCHAR(255) DEFAULT NULL,
  `pembahasan`       TEXT DEFAULT NULL,
  `bobot`            DECIMAL(6,2) NOT NULL DEFAULT 1.00,
  `urutan`           INT NOT NULL DEFAULT 0,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_soal_paket` (`paket_id`),
  KEY `idx_soal_sub` (`sub_kemampuan_id`),
  CONSTRAINT `fk_soal_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket_soal`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_soal_sub` FOREIGN KEY (`sub_kemampuan_id`) REFERENCES `sub_kemampuan`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opsi untuk PG/PGK. Untuk BS: tiap baris = 1 pernyataan, is_correct = nilai benar (1=Benar,0=Salah)
CREATE TABLE IF NOT EXISTS `soal_opsi` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `soal_id`    BIGINT UNSIGNED NOT NULL,
  `label`      VARCHAR(10) DEFAULT NULL,
  `teks`       TEXT NOT NULL,
  `gambar`     VARCHAR(255) DEFAULT NULL,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
  `urutan`     INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_opsi_soal` (`soal_id`),
  CONSTRAINT `fk_opsi_soal` FOREIGN KEY (`soal_id`) REFERENCES `soal`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  NARASI PENILAIAN (rentang nilai -> template narasi, dikelola Admin)
-- =====================================================================
CREATE TABLE IF NOT EXISTS `narasi_rentang` (
  `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label`     VARCHAR(100) NOT NULL,
  `min_nilai` DECIMAL(5,2) NOT NULL,
  `max_nilai` DECIMAL(5,2) NOT NULL,
  `template`  TEXT NOT NULL,
  `urutan`    INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  DELEGASI SOAL (admin -> siswa, per individu)
-- =====================================================================
CREATE TABLE IF NOT EXISTS `delegasi` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `paket_id`      BIGINT UNSIGNED NOT NULL,
  `siswa_id`      BIGINT UNSIGNED NOT NULL,
  `assigned_by`   BIGINT UNSIGNED NOT NULL,
  `waktu_mulai`   DATETIME DEFAULT NULL,
  `waktu_deadline` DATETIME DEFAULT NULL,
  `acak_soal`     TINYINT(1) NOT NULL DEFAULT 0,
  `boleh_review`  TINYINT(1) NOT NULL DEFAULT 1,
  `sumber`        ENUM('admin','pengajuan_ulang') NOT NULL DEFAULT 'admin',
  `status`        ENUM('assigned','ongoing','done','expired') NOT NULL DEFAULT 'assigned',
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_deleg_paket` (`paket_id`),
  KEY `idx_deleg_siswa` (`siswa_id`),
  KEY `idx_deleg_status` (`status`),
  CONSTRAINT `fk_deleg_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket_soal`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_deleg_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  PENGERJAAN (attempt) & JAWABAN
-- =====================================================================
CREATE TABLE IF NOT EXISTS `attempt` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `delegasi_id`   BIGINT UNSIGNED NOT NULL,
  `paket_id`      BIGINT UNSIGNED NOT NULL,
  `siswa_id`      BIGINT UNSIGNED NOT NULL,
  `attempt_ke`    INT NOT NULL DEFAULT 1,
  `nilai_total`   DECIMAL(5,2) DEFAULT NULL,
  `is_best`       TINYINT(1) NOT NULL DEFAULT 0,
  `status`        ENUM('ongoing','submitted') NOT NULL DEFAULT 'ongoing',
  `started_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submitted_at`  DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_attempt_deleg` (`delegasi_id`),
  KEY `idx_attempt_siswa` (`siswa_id`),
  KEY `idx_attempt_paket` (`paket_id`),
  CONSTRAINT `fk_attempt_deleg` FOREIGN KEY (`delegasi_id`) REFERENCES `delegasi`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attempt_jawaban` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `attempt_id`  BIGINT UNSIGNED NOT NULL,
  `soal_id`     BIGINT UNSIGNED NOT NULL,
  `jawaban`     TEXT DEFAULT NULL,         -- JSON: opsi terpilih / map pernyataan->B/S
  `skor`        DECIMAL(6,2) NOT NULL DEFAULT 0,
  `skor_maks`   DECIMAL(6,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jwb_attempt` (`attempt_id`),
  KEY `idx_jwb_soal` (`soal_id`),
  CONSTRAINT `fk_jwb_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `attempt`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hasil per sub kemampuan untuk satu attempt (dipakai report)
CREATE TABLE IF NOT EXISTS `attempt_subskor` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `attempt_id`       BIGINT UNSIGNED NOT NULL,
  `sub_kemampuan_id` BIGINT UNSIGNED NOT NULL,
  `nilai`            DECIMAL(5,2) NOT NULL DEFAULT 0,
  `narasi`           TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subskor_attempt` (`attempt_id`),
  CONSTRAINT `fk_subskor_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `attempt`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  PENGAJUAN KERJAKAN ULANG (siswa -> admin)
-- =====================================================================
CREATE TABLE IF NOT EXISTS `pengajuan_ulang` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id`      BIGINT UNSIGNED NOT NULL,
  `paket_id`      BIGINT UNSIGNED NOT NULL,
  `delegasi_asal` BIGINT UNSIGNED DEFAULT NULL,
  `alasan`        TEXT DEFAULT NULL,
  `status`        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `handled_by`    BIGINT UNSIGNED DEFAULT NULL,
  `handled_at`    DATETIME DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pu_status` (`status`),
  KEY `idx_pu_siswa` (`siswa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  PENGATURAN SISTEM (brand: nama, logo, alamat, no.telp, medsos)
-- =====================================================================
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `nama_key`  VARCHAR(80) NOT NULL,
  `nilai`     TEXT DEFAULT NULL,
  PRIMARY KEY (`nama_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
