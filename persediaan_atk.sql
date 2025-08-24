-- =====================================================================
-- Skrip SQL untuk Sistem Informasi Persediaan ATK
-- Database: db_atk_pa_penajam
-- Dibuat pada: 24 Agustus 2025
-- =====================================================================
-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_atk_pa_penajam;

USE db_atk_pa_penajam;

-- =====================================================================
-- 1. TABEL MASTER & PENGGUNA
-- =====================================================================
-- Tabel Kategori Barang
CREATE TABLE
    `tbl_kategori_barang` (
        `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_kategori` VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE = InnoDB COMMENT = 'Mengelompokkan jenis-jenis ATK';

-- Tabel Satuan Barang
CREATE TABLE
    `tbl_satuan_barang` (
        `id_satuan` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_satuan` VARCHAR(50) NOT NULL UNIQUE
    ) ENGINE = InnoDB COMMENT = 'Menyimpan satuan unit untuk setiap ATK';

-- Tabel Bagian/Departemen
CREATE TABLE
    `tbl_bagian` (
        `id_bagian` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_bagian` VARCHAR(150) NOT NULL UNIQUE
    ) ENGINE = InnoDB COMMENT = 'Struktur organisasi atau departemen di kantor';

-- Tabel Pemasok/Supplier
CREATE TABLE
    `tbl_pemasok` (
        `id_pemasok` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_pemasok` VARCHAR(255) NOT NULL,
        `alamat` TEXT,
        `no_telepon` VARCHAR(20),
        `email` VARCHAR(100)
    ) ENGINE = InnoDB COMMENT = 'Menyimpan data supplier atau vendor ATK';

-- Tabel Barang (ATK)
CREATE TABLE
    `tbl_barang` (
        `id_barang` INT AUTO_INCREMENT PRIMARY KEY,
        `kode_barang` VARCHAR(50) NOT NULL UNIQUE,
        `nama_barang` VARCHAR(255) NOT NULL,
        `id_kategori` INT NOT NULL,
        `id_satuan` INT NOT NULL,
        `stok_saat_ini` INT NOT NULL DEFAULT 0,
        `stok_minimal` INT NOT NULL DEFAULT 5,
        `lokasi_penyimpanan` VARCHAR(100),
        `spesifikasi` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`id_kategori`) REFERENCES `tbl_kategori_barang` (`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE,
        FOREIGN KEY (`id_satuan`) REFERENCES `tbl_satuan_barang` (`id_satuan`) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE = InnoDB COMMENT = 'Menyimpan semua data master Alat Tulis Kantor';

-- Tabel Pengguna Sistem
CREATE TABLE
    `tbl_pengguna` (
        `id_pengguna` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_lengkap` VARCHAR(255) NOT NULL,
        `username` VARCHAR(100) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL COMMENT 'Wajib di-hash',
        `id_bagian` INT NOT NULL,
        `role` ENUM ('admin', 'pegawai', 'pimpinan') NOT NULL,
        `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
        FOREIGN KEY (`id_bagian`) REFERENCES `tbl_bagian` (`id_bagian`) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE = InnoDB COMMENT = 'Mengelola semua pengguna yang dapat mengakses sistem';

-- =====================================================================
-- 2. TABEL TRANSAKSI & ALUR KERJA
-- =====================================================================
-- Tabel Barang Masuk (Header)
CREATE TABLE
    `tbl_barang_masuk` (
        `id_barang_masuk` INT AUTO_INCREMENT PRIMARY KEY,
        `no_transaksi_masuk` VARCHAR(50) NOT NULL UNIQUE,
        `id_pemasok` INT NOT NULL,
        `tanggal_masuk` DATE NOT NULL,
        `id_pengguna_penerima` INT NOT NULL,
        `keterangan` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`id_pemasok`) REFERENCES `tbl_pemasok` (`id_pemasok`),
        FOREIGN KEY (`id_pengguna_penerima`) REFERENCES `tbl_pengguna` (`id_pengguna`)
    ) ENGINE = InnoDB COMMENT = 'Mencatat transaksi penerimaan barang dari pemasok';

-- Tabel Detail Barang Masuk
CREATE TABLE
    `tbl_detail_barang_masuk` (
        `id_detail_masuk` INT AUTO_INCREMENT PRIMARY KEY,
        `id_barang_masuk` INT NOT NULL,
        `id_barang` INT NOT NULL,
        `jumlah_masuk` INT NOT NULL,
        `harga_satuan` DECIMAL(15, 2) DEFAULT 0.00,
        FOREIGN KEY (`id_barang_masuk`) REFERENCES `tbl_barang_masuk` (`id_barang_masuk`) ON DELETE CASCADE,
        FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`)
    ) ENGINE = InnoDB COMMENT = 'Menyimpan rincian item untuk setiap transaksi penerimaan';

-- Tabel Permintaan ATK (Header)
CREATE TABLE
    `tbl_permintaan_atk` (
        `id_permintaan` INT AUTO_INCREMENT PRIMARY KEY,
        `kode_permintaan` VARCHAR(50) NOT NULL UNIQUE,
        `id_pengguna_pemohon` INT NOT NULL,
        `tanggal_permintaan` DATE NOT NULL,
        `status_permintaan` ENUM ('Diajukan', 'Disetujui', 'Ditolak', 'Selesai') NOT NULL DEFAULT 'Diajukan',
        `id_pengguna_penyetuju` INT,
        `tanggal_diproses` DATETIME,
        `catatan_pemohon` TEXT,
        `catatan_penyetuju` TEXT,
        FOREIGN KEY (`id_pengguna_pemohon`) REFERENCES `tbl_pengguna` (`id_pengguna`),
        FOREIGN KEY (`id_pengguna_penyetuju`) REFERENCES `tbl_pengguna` (`id_pengguna`)
    ) ENGINE = InnoDB COMMENT = 'Mencatat setiap pengajuan permintaan ATK oleh pegawai';

-- Tabel Detail Permintaan ATK
CREATE TABLE
    `tbl_detail_permintaan_atk` (
        `id_detail_permintaan` INT AUTO_INCREMENT PRIMARY KEY,
        `id_permintaan` INT NOT NULL,
        `id_barang` INT NOT NULL,
        `jumlah_diminta` INT NOT NULL,
        `jumlah_disetujui` INT,
        FOREIGN KEY (`id_permintaan`) REFERENCES `tbl_permintaan_atk` (`id_permintaan`) ON DELETE CASCADE,
        FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`)
    ) ENGINE = InnoDB COMMENT = 'Menyimpan rincian item untuk setiap permintaan ATK';

-- Tabel Barang Keluar
CREATE TABLE
    `tbl_barang_keluar` (
        `id_barang_keluar` INT AUTO_INCREMENT PRIMARY KEY,
        `id_detail_permintaan` INT NOT NULL,
        `jumlah_keluar` INT NOT NULL,
        `tanggal_keluar` DATE NOT NULL,
        `id_admin_gudang` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`id_detail_permintaan`) REFERENCES `tbl_detail_permintaan_atk` (`id_detail_permintaan`),
        FOREIGN KEY (`id_admin_gudang`) REFERENCES `tbl_pengguna` (`id_pengguna`)
    ) ENGINE = InnoDB COMMENT = 'Mencatat pengeluaran barang dari gudang';

-- =====================================================================
-- 3. TABEL PENUNJANG
-- =====================================================================
-- Tabel Stock Opname (Header)
CREATE TABLE
    `tbl_stock_opname` (
        `id_opname` INT AUTO_INCREMENT PRIMARY KEY,
        `kode_opname` VARCHAR(50) NOT NULL UNIQUE,
        `tanggal_opname` DATE NOT NULL,
        `id_pengguna_penanggung_jawab` INT NOT NULL,
        `keterangan` TEXT,
        FOREIGN KEY (`id_pengguna_penanggung_jawab`) REFERENCES `tbl_pengguna` (`id_pengguna`)
    ) ENGINE = InnoDB COMMENT = 'Mencatat histori kegiatan stock opname';

-- Tabel Detail Stock Opname
CREATE TABLE
    `tbl_detail_stock_opname` (
        `id_detail_opname` INT AUTO_INCREMENT PRIMARY KEY,
        `id_opname` INT NOT NULL,
        `id_barang` INT NOT NULL,
        `stok_sistem` INT NOT NULL,
        `stok_fisik` INT NOT NULL,
        `selisih` INT NOT NULL,
        `tindakan_penyesuaian` TEXT,
        FOREIGN KEY (`id_opname`) REFERENCES `tbl_stock_opname` (`id_opname`) ON DELETE CASCADE,
        FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`)
    ) ENGINE = InnoDB COMMENT = 'Menyimpan rincian penyesuaian stok saat opname';

-- =====================================================================
-- CONTOH DATA AWAL (MASTER DATA)
-- =====================================================================
INSERT INTO
    `tbl_kategori_barang` (`nama_kategori`)
VALUES
    ('Kertas'),
    ('Alat Tulis'),
    ('Map & Arsip'),
    ('Tinta & Toner');

INSERT INTO
    `tbl_satuan_barang` (`nama_satuan`)
VALUES
    ('Pcs'),
    ('Box'),
    ('Rim'),
    ('Lusin'),
    ('Unit');

INSERT INTO
    `tbl_bagian` (`nama_bagian`)
VALUES
    ('Kepaniteraan'),
    ('Kesekretariatan'),
    ('Perencanaan, TI, dan Pelaporan'),
    ('Umum dan Keuangan');

-- 1. Tambah kolom baru di tabel barang
ALTER TABLE `tbl_barang` ADD `jenis_barang` ENUM ('habis_pakai', 'aset') NOT NULL DEFAULT 'habis_pakai' AFTER `nama_barang`;

-- 2. Buat tabel log stok
CREATE TABLE
    `tbl_log_stok` (
        `id_log` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `id_barang` INT NOT NULL,
        `jenis_transaksi` ENUM ('masuk', 'keluar', 'penyesuaian', 'dihapus') NOT NULL,
        `jumlah_ubah` INT NOT NULL,
        `stok_sebelum` INT NOT NULL,
        `stok_sesudah` INT NOT NULL,
        `id_referensi` INT NULL,
        `keterangan` VARCHAR(255) NULL,
        `id_pengguna_aksi` INT NULL,
        `tanggal_log` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE CASCADE
    ) ENGINE = InnoDB;
-- =====================================================================
-- Skrip SQL untuk Sistem Informasi Persediaan ATK
-- Database: db_atk_pa_penajam
-- Dibuat pada: 24 Agustus 2025
-- =====================================================================

-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_atk_pa_penajam;
USE db_atk_pa_penajam;

-- =====================================================================
-- 1. TABEL MASTER & PENGGUNA
-- =====================================================================

-- Tabel Kategori Barang
CREATE TABLE `tbl_kategori_barang` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB COMMENT='Mengelompokkan jenis-jenis ATK';

-- Tabel Satuan Barang
CREATE TABLE `tbl_satuan_barang` (
  `id_satuan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_satuan` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB COMMENT='Menyimpan satuan unit untuk setiap ATK';

-- Tabel Bagian/Departemen
CREATE TABLE `tbl_bagian` (
  `id_bagian` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_bagian` VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB COMMENT='Struktur organisasi atau departemen di kantor';

-- Tabel Pemasok/Supplier
CREATE TABLE `tbl_pemasok` (
  `id_pemasok` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pemasok` VARCHAR(255) NOT NULL,
  `alamat` TEXT,
  `no_telepon` VARCHAR(20),
  `email` VARCHAR(100)
) ENGINE=InnoDB COMMENT='Menyimpan data supplier atau vendor ATK';

-- Tabel Barang (ATK)
CREATE TABLE `tbl_barang` (
  `id_barang` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_barang` VARCHAR(50) NOT NULL UNIQUE,
  `nama_barang` VARCHAR(255) NOT NULL,
  `id_kategori` INT NOT NULL,
  `id_satuan` INT NOT NULL,
  `stok_saat_ini` INT NOT NULL DEFAULT 0,
  `stok_minimal` INT NOT NULL DEFAULT 5,
  `lokasi_penyimpanan` VARCHAR(100),
  `spesifikasi` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_kategori`) REFERENCES `tbl_kategori_barang`(`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`id_satuan`) REFERENCES `tbl_satuan_barang`(`id_satuan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Menyimpan semua data master Alat Tulis Kantor';

-- Tabel Pengguna Sistem
CREATE TABLE `tbl_pengguna` (
  `id_pengguna` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL COMMENT 'Wajib di-hash',
  `id_bagian` INT NOT NULL,
  `role` ENUM('admin', 'pegawai', 'pimpinan') NOT NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  FOREIGN KEY (`id_bagian`) REFERENCES `tbl_bagian`(`id_bagian`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Mengelola semua pengguna yang dapat mengakses sistem';

-- =====================================================================
-- 2. TABEL TRANSAKSI & ALUR KERJA
-- =====================================================================

-- Tabel Barang Masuk (Header)
CREATE TABLE `tbl_barang_masuk` (
  `id_barang_masuk` INT AUTO_INCREMENT PRIMARY KEY,
  `no_transaksi_masuk` VARCHAR(50) NOT NULL UNIQUE,
  `id_pemasok` INT NOT NULL,
  `tanggal_masuk` DATE NOT NULL,
  `id_pengguna_penerima` INT NOT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_pemasok`) REFERENCES `tbl_pemasok`(`id_pemasok`),
  FOREIGN KEY (`id_pengguna_penerima`) REFERENCES `tbl_pengguna`(`id_pengguna`)
) ENGINE=InnoDB COMMENT='Mencatat transaksi penerimaan barang dari pemasok';

-- Tabel Detail Barang Masuk
CREATE TABLE `tbl_detail_barang_masuk` (
  `id_detail_masuk` INT AUTO_INCREMENT PRIMARY KEY,
  `id_barang_masuk` INT NOT NULL,
  `id_barang` INT NOT NULL,
  `jumlah_masuk` INT NOT NULL,
  `harga_satuan` DECIMAL(15,2) DEFAULT 0.00,
  FOREIGN KEY (`id_barang_masuk`) REFERENCES `tbl_barang_masuk`(`id_barang_masuk`) ON DELETE CASCADE,
  FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang`(`id_barang`)
) ENGINE=InnoDB COMMENT='Menyimpan rincian item untuk setiap transaksi penerimaan';

-- Tabel Permintaan ATK (Header)
CREATE TABLE `tbl_permintaan_atk` (
  `id_permintaan` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_permintaan` VARCHAR(50) NOT NULL UNIQUE,
  `id_pengguna_pemohon` INT NOT NULL,
  `tanggal_permintaan` DATE NOT NULL,
  `status_permintaan` ENUM('Diajukan', 'Disetujui', 'Ditolak', 'Selesai') NOT NULL DEFAULT 'Diajukan',
  `id_pengguna_penyetuju` INT,
  `tanggal_diproses` DATETIME,
  `catatan_pemohon` TEXT,
  `catatan_penyetuju` TEXT,
  FOREIGN KEY (`id_pengguna_pemohon`) REFERENCES `tbl_pengguna`(`id_pengguna`),
  FOREIGN KEY (`id_pengguna_penyetuju`) REFERENCES `tbl_pengguna`(`id_pengguna`)
) ENGINE=InnoDB COMMENT='Mencatat setiap pengajuan permintaan ATK oleh pegawai';

-- Tabel Detail Permintaan ATK
CREATE TABLE `tbl_detail_permintaan_atk` (
  `id_detail_permintaan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_permintaan` INT NOT NULL,
  `id_barang` INT NOT NULL,
  `jumlah_diminta` INT NOT NULL,
  `jumlah_disetujui` INT,
  FOREIGN KEY (`id_permintaan`) REFERENCES `tbl_permintaan_atk`(`id_permintaan`) ON DELETE CASCADE,
  FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang`(`id_barang`)
) ENGINE=InnoDB COMMENT='Menyimpan rincian item untuk setiap permintaan ATK';

-- Tabel Barang Keluar
CREATE TABLE `tbl_barang_keluar` (
  `id_barang_keluar` INT AUTO_INCREMENT PRIMARY KEY,
  `id_detail_permintaan` INT NOT NULL,
  `jumlah_keluar` INT NOT NULL,
  `tanggal_keluar` DATE NOT NULL,
  `id_admin_gudang` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_detail_permintaan`) REFERENCES `tbl_detail_permintaan_atk`(`id_detail_permintaan`),
  FOREIGN KEY (`id_admin_gudang`) REFERENCES `tbl_pengguna`(`id_pengguna`)
) ENGINE=InnoDB COMMENT='Mencatat pengeluaran barang dari gudang';

-- =====================================================================
-- 3. TABEL PENUNJANG
-- =====================================================================

-- Tabel Stock Opname (Header)
CREATE TABLE `tbl_stock_opname` (
  `id_opname` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_opname` VARCHAR(50) NOT NULL UNIQUE,
  `tanggal_opname` DATE NOT NULL,
  `id_pengguna_penanggung_jawab` INT NOT NULL,
  `keterangan` TEXT,
  FOREIGN KEY (`id_pengguna_penanggung_jawab`) REFERENCES `tbl_pengguna`(`id_pengguna`)
) ENGINE=InnoDB COMMENT='Mencatat histori kegiatan stock opname';

-- Tabel Detail Stock Opname
CREATE TABLE `tbl_detail_stock_opname` (
  `id_detail_opname` INT AUTO_INCREMENT PRIMARY KEY,
  `id_opname` INT NOT NULL,
  `id_barang` INT NOT NULL,
  `stok_sistem` INT NOT NULL,
  `stok_fisik` INT NOT NULL,
  `selisih` INT NOT NULL,
  `tindakan_penyesuaian` TEXT,
  FOREIGN KEY (`id_opname`) REFERENCES `tbl_stock_opname`(`id_opname`) ON DELETE CASCADE,
  FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang`(`id_barang`)
) ENGINE=InnoDB COMMENT='Menyimpan rincian penyesuaian stok saat opname';

-- =====================================================================
-- CONTOH DATA AWAL (MASTER DATA)
-- =====================================================================

INSERT INTO `tbl_kategori_barang` (`nama_kategori`) VALUES
('Kertas'),
('Alat Tulis'),
('Map & Arsip'),
('Tinta & Toner');

INSERT INTO `tbl_satuan_barang` (`nama_satuan`) VALUES
('Pcs'),
('Box'),
('Rim'),
('Lusin'),
('Unit');

INSERT INTO `tbl_bagian` (`nama_bagian`) VALUES
('Kepaniteraan'),
('Kesekretariatan'),
('Perencanaan, TI, dan Pelaporan'),
('Umum dan Keuangan');
-- 1. Tambah kolom baru di tabel barang
ALTER TABLE `tbl_barang`
ADD `jenis_barang` ENUM('habis_pakai', 'aset') NOT NULL DEFAULT 'habis_pakai' AFTER `nama_barang`;

-- 2. Buat tabel log stok
CREATE TABLE `tbl_log_stok` (
  `id_log` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `id_barang` INT NOT NULL,
  `jenis_transaksi` ENUM('masuk', 'keluar', 'penyesuaian', 'dihapus') NOT NULL,
  `jumlah_ubah` INT NOT NULL,
  `stok_sebelum` INT NOT NULL,
  `stok_sesudah` INT NOT NULL,
  `id_referensi` INT NULL,
  `keterangan` VARCHAR(255) NULL,
  `id_pengguna_aksi` INT NULL,
  `tanggal_log` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang`(`id_barang`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Buat Trigger untuk INSERT barang baru
DELIMITER $$
CREATE TRIGGER after_barang_insert
AFTER INSERT ON tbl_barang
FOR EACH ROW
BEGIN
    INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan, id_pengguna_aksi)
    VALUES (NEW.id_barang, 'penyesuaian', NEW.stok_saat_ini, 0, NEW.stok_saat_ini, 'Stok awal barang baru', NULL);
END$$
DELIMITER ;

-- 4. Buat Trigger untuk UPDATE stok barang
DELIMITER $$
CREATE TRIGGER after_barang_update
AFTER UPDATE ON tbl_barang
FOR EACH ROW
BEGIN
    IF OLD.stok_saat_ini <> NEW.stok_saat_ini THEN
        INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan, id_pengguna_aksi)
        VALUES (NEW.id_barang, 'penyesuaian', (NEW.stok_saat_ini - OLD.stok_saat_ini), OLD.stok_saat_ini, NEW.stok_saat_ini, 'Penyesuaian stok manual', NULL);
    END IF;
END$$
DELIMITER ;

-- 5. Buat Trigger untuk DELETE barang
DELIMITER $$
CREATE TRIGGER before_barang_delete
BEFORE DELETE ON tbl_barang
FOR EACH ROW
BEGIN
    INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan, id_pengguna_aksi)
    VALUES (OLD.id_barang, 'dihapus', -OLD.stok_saat_ini, OLD.stok_saat_ini, 0, 'Barang dihapus dari sistem', NULL);
END$$
DELIMITER ;
-- Contoh Pengguna (password: '123456', harus di-hash di aplikasi)
-- Hash untuk '123456' adalah: $2y$10$9.p2aVot1e.Y.Pu0x01v5e.U.fA7w3.f5g.h6i.j7k.l8m.n9o
-- Anda harus menggantinya dengan hash yang dihasilkan oleh aplikasi Anda.
-- INSERT INTO `tbl_pengguna` (`nama_lengkap`, `username`, `password`, `id_bagian`, `role`) VALUES
-- ('Administrator', 'admin', '$2y$10$....', 3, 'admin'),
-- ('Pegawai A', 'pegawai1', '$2y$10$....', 1, 'pegawai'),
-- ('Pimpinan', 'pimpinan', '$2y$10$....', 2, 'pimpinan');

-- =====================================================================
-- AKHIR DARI SKRIP
-- =====================================================================

-- Contoh Pengguna (password: '123456', harus di-hash di aplikasi)
-- Hash untuk '123456' adalah: $2y$10$9.p2aVot1e.Y.Pu0x01v5e.U.fA7w3.f5g.h6i.j7k.l8m.n9o
-- Anda harus menggantinya dengan hash yang dihasilkan oleh aplikasi Anda.
-- INSERT INTO `tbl_pengguna` (`nama_lengkap`, `username`, `password`, `id_bagian`, `role`) VALUES
-- ('Administrator', 'admin', '$2y$10$....', 3, 'admin'),
-- ('Pegawai A', 'pegawai1', '$2y$10$....', 1, 'pegawai'),
-- ('Pimpinan', 'pimpinan', '$2y$10$....', 2, 'pimpinan');
-- =====================================================================
-- AKHIR DARI SKRIP
-- =====================================================================