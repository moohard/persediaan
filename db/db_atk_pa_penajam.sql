/*
 Navicat Premium Dump SQL

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 110802 (11.8.2-MariaDB-log)
 Source Host           : localhost:3306
 Source Schema         : db_atk_pa_penajam

 Target Server Type    : MySQL
 Target Server Version : 110802 (11.8.2-MariaDB-log)
 File Encoding         : 65001

 Date: 02/09/2025 06:28:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for phinxlog
-- ----------------------------
DROP TABLE IF EXISTS `phinxlog`;
CREATE TABLE `phinxlog`  (
  `version` bigint NOT NULL,
  `migration_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of phinxlog
-- ----------------------------
INSERT INTO `phinxlog` VALUES (20250831035246, 'ConsolidatedSchema', '2025-08-31 07:27:59', '2025-08-31 07:27:59', 0);
INSERT INTO `phinxlog` VALUES (20250831093416, 'ImplementRbacSystem', '2025-08-31 07:27:59', '2025-08-31 07:27:59', 0);
INSERT INTO `phinxlog` VALUES (20250831113620, 'AddUserManagementPermissions', '2025-08-31 11:39:51', '2025-08-31 11:39:51', 0);
INSERT INTO `phinxlog` VALUES (20250831122507, 'AddReportingPermissions', '2025-08-31 12:26:25', '2025-08-31 12:26:25', 0);
INSERT INTO `phinxlog` VALUES (20250901151549, 'UpdateLogStokSchema', '2025-09-01 15:19:48', '2025-09-01 15:19:48', 0);
INSERT INTO `phinxlog` VALUES (20250901152533, 'AddSettingsPermission', '2025-09-01 17:24:36', '2025-09-01 17:24:36', 0);

-- ----------------------------
-- Table structure for tbl_bagian
-- ----------------------------
DROP TABLE IF EXISTS `tbl_bagian`;
CREATE TABLE `tbl_bagian`  (
  `id_bagian` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_bagian` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_bagian`) USING BTREE,
  UNIQUE INDEX `nama_bagian`(`nama_bagian` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_bagian
-- ----------------------------
INSERT INTO `tbl_bagian` VALUES (2, 'Kepegawaian & Ortala');
INSERT INTO `tbl_bagian` VALUES (1, 'Umum & Keuangan');

-- ----------------------------
-- Table structure for tbl_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_barang`;
CREATE TABLE `tbl_barang`  (
  `id_barang` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jenis_barang` enum('habis_pakai','aset') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'habis_pakai',
  `id_kategori` int UNSIGNED NULL DEFAULT NULL,
  `id_satuan` int UNSIGNED NULL DEFAULT NULL,
  `stok_umum` int UNSIGNED NULL DEFAULT 0,
  `stok_perkara` int UNSIGNED NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_barang`) USING BTREE,
  UNIQUE INDEX `kode_barang`(`kode_barang` ASC) USING BTREE,
  INDEX `deleted_at`(`deleted_at` ASC) USING BTREE,
  INDEX `id_kategori`(`id_kategori` ASC) USING BTREE,
  INDEX `id_satuan`(`id_satuan` ASC) USING BTREE,
  CONSTRAINT `tbl_barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `tbl_kategori_barang` (`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tbl_barang_ibfk_2` FOREIGN KEY (`id_satuan`) REFERENCES `tbl_satuan_barang` (`id_satuan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_barang
-- ----------------------------
INSERT INTO `tbl_barang` VALUES (1, 'ATK-KRT-001', 'Kertas HVS A4 70gr', 'habis_pakai', 2, 2, 0, 0, '2025-08-31 23:13:07', '2025-08-31 17:50:03', '2025-09-02 01:25:31');
INSERT INTO `tbl_barang` VALUES (2, 'ATK-ALT-001', 'Pulpen Tinta Hitam', 'habis_pakai', 1, 1, 0, 0, '2025-08-31 23:13:12', '2025-08-31 17:50:03', '2025-09-02 01:25:31');
INSERT INTO `tbl_barang` VALUES (3, 'ATK-PLP-001', 'Pulpen Joyko', 'habis_pakai', 1, 1, 0, 0, NULL, '2025-08-31 20:19:04', '2025-09-02 01:25:31');

-- ----------------------------
-- Table structure for tbl_barang_keluar
-- ----------------------------
DROP TABLE IF EXISTS `tbl_barang_keluar`;
CREATE TABLE `tbl_barang_keluar`  (
  `id_barang_keluar` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_detail_permintaan` int UNSIGNED NULL DEFAULT NULL,
  `jumlah_keluar` int UNSIGNED NULL DEFAULT NULL,
  `tanggal_keluar` date NULL DEFAULT NULL,
  `id_admin_gudang` int UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_barang_keluar`) USING BTREE,
  INDEX `id_detail_permintaan`(`id_detail_permintaan` ASC) USING BTREE,
  INDEX `id_admin_gudang`(`id_admin_gudang` ASC) USING BTREE,
  CONSTRAINT `tbl_barang_keluar_ibfk_1` FOREIGN KEY (`id_detail_permintaan`) REFERENCES `tbl_detail_permintaan_atk` (`id_detail_permintaan`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tbl_barang_keluar_ibfk_2` FOREIGN KEY (`id_admin_gudang`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_barang_keluar
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_barang_masuk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_barang_masuk`;
CREATE TABLE `tbl_barang_masuk`  (
  `id_barang_masuk` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_transaksi_masuk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_pemasok` int UNSIGNED NULL DEFAULT NULL,
  `tanggal_masuk` date NULL DEFAULT NULL,
  `id_pengguna_penerima` int UNSIGNED NULL DEFAULT NULL,
  `id_permintaan_terkait` int UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id_barang_masuk`) USING BTREE,
  UNIQUE INDEX `no_transaksi_masuk`(`no_transaksi_masuk` ASC) USING BTREE,
  INDEX `id_pemasok`(`id_pemasok` ASC) USING BTREE,
  INDEX `id_pengguna_penerima`(`id_pengguna_penerima` ASC) USING BTREE,
  INDEX `id_permintaan_terkait`(`id_permintaan_terkait` ASC) USING BTREE,
  CONSTRAINT `tbl_barang_masuk_ibfk_1` FOREIGN KEY (`id_pemasok`) REFERENCES `tbl_pemasok` (`id_pemasok`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tbl_barang_masuk_ibfk_2` FOREIGN KEY (`id_pengguna_penerima`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tbl_barang_masuk_ibfk_3` FOREIGN KEY (`id_permintaan_terkait`) REFERENCES `tbl_permintaan_atk` (`id_permintaan`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_barang_masuk
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_detail_barang_masuk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_detail_barang_masuk`;
CREATE TABLE `tbl_detail_barang_masuk`  (
  `id_detail_masuk` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang_masuk` int UNSIGNED NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `jumlah_diterima` int UNSIGNED NULL DEFAULT NULL,
  `jumlah_umum` int UNSIGNED NULL DEFAULT 0,
  `jumlah_perkara` int UNSIGNED NULL DEFAULT 0,
  PRIMARY KEY (`id_detail_masuk`) USING BTREE,
  INDEX `id_barang_masuk`(`id_barang_masuk` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `tbl_detail_barang_masuk_ibfk_1` FOREIGN KEY (`id_barang_masuk`) REFERENCES `tbl_barang_masuk` (`id_barang_masuk`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `tbl_detail_barang_masuk_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_detail_barang_masuk
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_detail_permintaan_atk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_detail_permintaan_atk`;
CREATE TABLE `tbl_detail_permintaan_atk`  (
  `id_detail_permintaan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_permintaan` int UNSIGNED NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `nama_barang_custom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumlah_diminta` int UNSIGNED NULL DEFAULT NULL,
  `jumlah_disetujui` int UNSIGNED NULL DEFAULT NULL,
  `status_item` enum('Selesai Diterima') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_barang_masuk_detail` int UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail_permintaan`) USING BTREE,
  INDEX `id_permintaan`(`id_permintaan` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `tbl_detail_permintaan_atk_ibfk_1` FOREIGN KEY (`id_permintaan`) REFERENCES `tbl_permintaan_atk` (`id_permintaan`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `tbl_detail_permintaan_atk_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_detail_permintaan_atk
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_kategori_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kategori_barang`;
CREATE TABLE `tbl_kategori_barang`  (
  `id_kategori` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_kategori`) USING BTREE,
  UNIQUE INDEX `nama_kategori`(`nama_kategori` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_kategori_barang
-- ----------------------------
INSERT INTO `tbl_kategori_barang` VALUES (1, 'Alat Tulis');
INSERT INTO `tbl_kategori_barang` VALUES (2, 'Kertas');

-- ----------------------------
-- Table structure for tbl_log_stok
-- ----------------------------
DROP TABLE IF EXISTS `tbl_log_stok`;
CREATE TABLE `tbl_log_stok`  (
  `id_log` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `jenis_transaksi` enum('masuk','keluar','penyesuaian','dihapus') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumlah_ubah` int NULL DEFAULT NULL,
  `stok_sebelum_umum` int UNSIGNED NULL DEFAULT NULL,
  `stok_sesudah_umum` int UNSIGNED NULL DEFAULT NULL,
  `stok_sebelum_perkara` int UNSIGNED NULL DEFAULT NULL,
  `stok_sesudah_perkara` int UNSIGNED NULL DEFAULT NULL,
  `id_referensi` int UNSIGNED NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_pengguna_aksi` int UNSIGNED NULL DEFAULT NULL,
  `tanggal_log` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `tbl_log_stok_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_log_stok
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_pemasok
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pemasok`;
CREATE TABLE `tbl_pemasok`  (
  `id_pemasok` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pemasok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemasok`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_pemasok
-- ----------------------------
INSERT INTO `tbl_pemasok` VALUES (1, 'Pemasok Umum', '-', NULL, NULL);

-- ----------------------------
-- Table structure for tbl_pengguna
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pengguna`;
CREATE TABLE `tbl_pengguna`  (
  `id_pengguna` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_bagian` int UNSIGNED NULL DEFAULT NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `id_role` int UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengguna`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE,
  INDEX `id_bagian`(`id_bagian` ASC) USING BTREE,
  INDEX `id_role`(`id_role` ASC) USING BTREE,
  CONSTRAINT `tbl_pengguna_ibfk_1` FOREIGN KEY (`id_bagian`) REFERENCES `tbl_bagian` (`id_bagian`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tbl_pengguna_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `tbl_roles` (`id_role`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 101 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_pengguna
-- ----------------------------
INSERT INTO `tbl_pengguna` VALUES (1, 'Admin Gudang', 'admin', '$2y$12$KQpFr22dnJLsn0/h77chGegilv3lGlp4rM75TvyPUipqJfoxC2ksi', 1, 1, 2);
INSERT INTO `tbl_pengguna` VALUES (2, 'Pimpinan PA', 'pimpinan', '$2y$12$WlTJGYxNHFhYOYS4BHf5VeKm9A2rLuE1a8bpToHbo9PJzvyfNtnS6', 1, 1, 3);
INSERT INTO `tbl_pengguna` VALUES (3, 'Pegawai Staff', 'pegawai', '$2y$12$Q0xhRHSkqEgXWxrAheY5BuV9bYZXw7ShRPmDdhgkN7SRorGjaMGrW', 2, 1, 4);
INSERT INTO `tbl_pengguna` VALUES (99, 'Developer', 'developer', '$2y$12$UAQ.q.U.59r7/PnLcqpgoeFqxW8oDBt2aEkGTaD2N4v3Bfakmthnq', 1, 1, 1);

-- ----------------------------
-- Table structure for tbl_permintaan_atk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_permintaan_atk`;
CREATE TABLE `tbl_permintaan_atk`  (
  `id_permintaan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_permintaan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_pengguna_pemohon` int UNSIGNED NULL DEFAULT NULL,
  `tanggal_permintaan` date NULL DEFAULT NULL,
  `tipe_permintaan` enum('stok','pembelian') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'stok',
  `status_permintaan` enum('Diajukan','Disetujui','Ditolak','Selesai','Diproses Pembelian','Sudah Dibeli') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Diajukan',
  `catatan_pemohon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `id_pengguna_penyetuju` int UNSIGNED NULL DEFAULT NULL,
  `tanggal_diproses` datetime NULL DEFAULT NULL,
  `catatan_penyetuju` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `id_pengguna_pembelian` int UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_permintaan`) USING BTREE,
  UNIQUE INDEX `kode_permintaan`(`kode_permintaan` ASC) USING BTREE,
  INDEX `id_pengguna_pemohon`(`id_pengguna_pemohon` ASC) USING BTREE,
  INDEX `id_pengguna_penyetuju`(`id_pengguna_penyetuju` ASC) USING BTREE,
  INDEX `id_pengguna_pembelian`(`id_pengguna_pembelian` ASC) USING BTREE,
  CONSTRAINT `tbl_permintaan_atk_ibfk_1` FOREIGN KEY (`id_pengguna_pemohon`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tbl_permintaan_atk_ibfk_2` FOREIGN KEY (`id_pengguna_penyetuju`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tbl_permintaan_atk_ibfk_3` FOREIGN KEY (`id_pengguna_pembelian`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_permintaan_atk
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_permissions
-- ----------------------------
DROP TABLE IF EXISTS `tbl_permissions`;
CREATE TABLE `tbl_permissions`  (
  `id_permission` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_permission` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `deskripsi_permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `grup` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'lainnya',
  PRIMARY KEY (`id_permission`) USING BTREE,
  UNIQUE INDEX `nama_permission`(`nama_permission` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_permissions
-- ----------------------------
INSERT INTO `tbl_permissions` VALUES (1, 'barang_view', 'Melihat daftar barang', 'Barang');
INSERT INTO `tbl_permissions` VALUES (2, 'barang_create', 'Membuat data barang baru', 'Barang');
INSERT INTO `tbl_permissions` VALUES (3, 'barang_update', 'Mengubah data barang', 'Barang');
INSERT INTO `tbl_permissions` VALUES (4, 'barang_delete', 'Menghapus (soft delete) data barang', 'Barang');
INSERT INTO `tbl_permissions` VALUES (5, 'barang_trash', 'Melihat dan memulihkan barang dari sampah', 'Barang');
INSERT INTO `tbl_permissions` VALUES (6, 'permintaan_view_all', 'Melihat semua permintaan', 'Permintaan');
INSERT INTO `tbl_permissions` VALUES (7, 'permintaan_view_own', 'Melihat permintaan milik sendiri', 'Permintaan');
INSERT INTO `tbl_permissions` VALUES (8, 'permintaan_create', 'Membuat permintaan baru', 'Permintaan');
INSERT INTO `tbl_permissions` VALUES (9, 'permintaan_approve', 'Menyetujui atau menolak permintaan', 'Permintaan');
INSERT INTO `tbl_permissions` VALUES (10, 'pembelian_process', 'Memproses permintaan pembelian', 'Pembelian');
INSERT INTO `tbl_permissions` VALUES (11, 'barangmasuk_process', 'Memproses penerimaan barang', 'Pembelian');
INSERT INTO `tbl_permissions` VALUES (12, 'log_view', 'Melihat log query SQL', 'Developer');
INSERT INTO `tbl_permissions` VALUES (13, 'user_management_view', 'Melihat daftar pengguna', 'Pengguna');
INSERT INTO `tbl_permissions` VALUES (14, 'user_management_create', 'Membuat pengguna baru', 'Pengguna');
INSERT INTO `tbl_permissions` VALUES (15, 'user_management_update', 'Mengubah data pengguna', 'Pengguna');
INSERT INTO `tbl_permissions` VALUES (16, 'user_management_delete', 'Menghapus pengguna', 'Pengguna');
INSERT INTO `tbl_permissions` VALUES (17, 'laporan_view', 'Melihat halaman laporan', 'Laporan');
INSERT INTO `tbl_permissions` VALUES (18, 'laporan_stok_print', 'Mencetak laporan stok barang', 'Laporan');
INSERT INTO `tbl_permissions` VALUES (19, 'laporan_kartu_stok_view', 'Melihat detail riwayat barang (Kartu Stok)', 'Laporan');
INSERT INTO `tbl_permissions` VALUES (20, 'laporan_kartu_stok_print', 'Mencetak/Ekspor data Kartu Stok', 'Laporan');
INSERT INTO `tbl_permissions` VALUES (21, 'pengaturan_view', 'Melihat halaman pengaturan', 'Pengaturan');
INSERT INTO `tbl_permissions` VALUES (22, 'pengaturan_clear_transactions', 'Mengosongkan semua data transaksi', 'Pengaturan');

-- ----------------------------
-- Table structure for tbl_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `tbl_role_permissions`;
CREATE TABLE `tbl_role_permissions`  (
  `id_role` int UNSIGNED NOT NULL,
  `id_permission` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_role`, `id_permission`) USING BTREE,
  INDEX `id_permission`(`id_permission` ASC) USING BTREE,
  CONSTRAINT `tbl_role_permissions_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `tbl_roles` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_role_permissions_ibfk_2` FOREIGN KEY (`id_permission`) REFERENCES `tbl_permissions` (`id_permission`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_role_permissions
-- ----------------------------
INSERT INTO `tbl_role_permissions` VALUES (1, 1);
INSERT INTO `tbl_role_permissions` VALUES (2, 1);
INSERT INTO `tbl_role_permissions` VALUES (1, 2);
INSERT INTO `tbl_role_permissions` VALUES (2, 2);
INSERT INTO `tbl_role_permissions` VALUES (1, 3);
INSERT INTO `tbl_role_permissions` VALUES (2, 3);
INSERT INTO `tbl_role_permissions` VALUES (1, 4);
INSERT INTO `tbl_role_permissions` VALUES (2, 4);
INSERT INTO `tbl_role_permissions` VALUES (1, 5);
INSERT INTO `tbl_role_permissions` VALUES (2, 5);
INSERT INTO `tbl_role_permissions` VALUES (1, 6);
INSERT INTO `tbl_role_permissions` VALUES (2, 6);
INSERT INTO `tbl_role_permissions` VALUES (3, 6);
INSERT INTO `tbl_role_permissions` VALUES (4, 7);
INSERT INTO `tbl_role_permissions` VALUES (1, 8);
INSERT INTO `tbl_role_permissions` VALUES (4, 8);
INSERT INTO `tbl_role_permissions` VALUES (1, 9);
INSERT INTO `tbl_role_permissions` VALUES (3, 9);
INSERT INTO `tbl_role_permissions` VALUES (1, 10);
INSERT INTO `tbl_role_permissions` VALUES (2, 10);
INSERT INTO `tbl_role_permissions` VALUES (1, 11);
INSERT INTO `tbl_role_permissions` VALUES (2, 11);
INSERT INTO `tbl_role_permissions` VALUES (1, 12);
INSERT INTO `tbl_role_permissions` VALUES (1, 13);
INSERT INTO `tbl_role_permissions` VALUES (2, 13);
INSERT INTO `tbl_role_permissions` VALUES (1, 14);
INSERT INTO `tbl_role_permissions` VALUES (2, 14);
INSERT INTO `tbl_role_permissions` VALUES (1, 15);
INSERT INTO `tbl_role_permissions` VALUES (2, 15);
INSERT INTO `tbl_role_permissions` VALUES (1, 16);
INSERT INTO `tbl_role_permissions` VALUES (2, 16);
INSERT INTO `tbl_role_permissions` VALUES (1, 17);
INSERT INTO `tbl_role_permissions` VALUES (3, 17);
INSERT INTO `tbl_role_permissions` VALUES (1, 18);
INSERT INTO `tbl_role_permissions` VALUES (3, 18);
INSERT INTO `tbl_role_permissions` VALUES (1, 19);
INSERT INTO `tbl_role_permissions` VALUES (2, 19);
INSERT INTO `tbl_role_permissions` VALUES (3, 19);
INSERT INTO `tbl_role_permissions` VALUES (1, 20);
INSERT INTO `tbl_role_permissions` VALUES (2, 20);
INSERT INTO `tbl_role_permissions` VALUES (3, 20);
INSERT INTO `tbl_role_permissions` VALUES (1, 21);
INSERT INTO `tbl_role_permissions` VALUES (1, 22);

-- ----------------------------
-- Table structure for tbl_roles
-- ----------------------------
DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE `tbl_roles`  (
  `id_role` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `deskripsi_role` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id_role`) USING BTREE,
  UNIQUE INDEX `nama_role`(`nama_role` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_roles
-- ----------------------------
INSERT INTO `tbl_roles` VALUES (1, 'Developer', 'Akses penuh ke semua fitur sistem untuk pengembangan.');
INSERT INTO `tbl_roles` VALUES (2, 'Admin', 'Akses untuk tugas operasional harian.');
INSERT INTO `tbl_roles` VALUES (3, 'Pimpinan', 'Akses untuk menyetujui permintaan.');
INSERT INTO `tbl_roles` VALUES (4, 'Pegawai', 'Akses terbatas untuk membuat permintaan.');

-- ----------------------------
-- Table structure for tbl_satuan_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_satuan_barang`;
CREATE TABLE `tbl_satuan_barang`  (
  `id_satuan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_satuan`) USING BTREE,
  UNIQUE INDEX `nama_satuan`(`nama_satuan` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_satuan_barang
-- ----------------------------
INSERT INTO `tbl_satuan_barang` VALUES (1, 'Pcs');
INSERT INTO `tbl_satuan_barang` VALUES (2, 'Rim');

-- ----------------------------
-- View structure for v_permintaan_lengkap
-- ----------------------------
DROP VIEW IF EXISTS `v_permintaan_lengkap`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_permintaan_lengkap` AS SELECT 
                p.*,
                u.nama_lengkap AS nama_pemohon,
                a.nama_lengkap AS nama_penyetuju,
                (SELECT COUNT(*) FROM tbl_detail_permintaan_atk WHERE id_permintaan = p.id_permintaan) as jumlah_item
            FROM tbl_permintaan_atk p
            JOIN tbl_pengguna u ON p.id_pengguna_pemohon = u.id_pengguna
            LEFT JOIN tbl_pengguna a ON p.id_pengguna_penyetuju = a.id_pengguna ;

-- ----------------------------
-- Triggers structure for table tbl_barang
-- ----------------------------
DROP TRIGGER IF EXISTS `before_barang_delete`;
delimiter ;;
CREATE TRIGGER `before_barang_delete` BEFORE DELETE ON `tbl_barang` FOR EACH ROW BEGIN
                INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum_total, stok_sesudah_total, keterangan, id_pengguna_aksi, tanggal_log)
                VALUES (OLD.id_barang, 'dihapus', -(OLD.stok_umum + OLD.stok_perkara), (OLD.stok_umum + OLD.stok_perkara), 0, CONCAT('Penghapusan permanen barang: ', OLD.nama_barang), @user_id, NOW());
            END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
