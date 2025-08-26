/*
 Navicat Premium Dump SQL

 Source Server         : local-112
 Source Server Type    : MariaDB
 Source Server Version : 110802 (11.8.2-MariaDB-log)
 Source Host           : localhost:3306
 Source Schema         : db_atk_pa_penajam_dev

 Target Server Type    : MariaDB
 Target Server Version : 110802 (11.8.2-MariaDB-log)
 File Encoding         : 65001

 Date: 26/08/2025 22:35:45
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for phinxlog
-- ----------------------------
DROP TABLE IF EXISTS `phinxlog`;
CREATE TABLE `phinxlog`  (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of phinxlog
-- ----------------------------
INSERT INTO `phinxlog` VALUES (20250824163145, 'CreateInitialSchema', '2025-08-25 01:35:56', '2025-08-25 01:35:57', 0);
INSERT INTO `phinxlog` VALUES (20250825020202, 'AddPurchaseRequestType', '2025-08-25 02:05:30', '2025-08-25 02:05:30', 0);
INSERT INTO `phinxlog` VALUES (20250825023937, 'AddCustomItemsAndPurchaseStatus', '2025-08-25 02:40:20', '2025-08-25 02:40:20', 0);
INSERT INTO `phinxlog` VALUES (20250825032327, 'AddPurchaseWorkflowStatus', '2025-08-25 03:23:58', '2025-08-25 03:23:58', 0);
INSERT INTO `phinxlog` VALUES (20250826071859, 'AddDeveloperRole', '2025-08-26 07:19:54', '2025-08-26 07:19:54', 0);

-- ----------------------------
-- Table structure for tbl_bagian
-- ----------------------------
DROP TABLE IF EXISTS `tbl_bagian`;
CREATE TABLE `tbl_bagian`  (
  `id_bagian` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_bagian` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_bagian`) USING BTREE,
  UNIQUE INDEX `nama_bagian`(`nama_bagian` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_bagian
-- ----------------------------
INSERT INTO `tbl_bagian` VALUES (1, 'Kepaniteraan');
INSERT INTO `tbl_bagian` VALUES (2, 'Kesekretariatan');
INSERT INTO `tbl_bagian` VALUES (3, 'Perencanaan, TI, dan Pelaporan');
INSERT INTO `tbl_bagian` VALUES (4, 'Umum dan Keuangan');

-- ----------------------------
-- Table structure for tbl_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_barang`;
CREATE TABLE `tbl_barang`  (
  `id_barang` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jenis_barang` enum('habis_pakai','aset') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'habis_pakai',
  `id_kategori` int(11) UNSIGNED NULL DEFAULT NULL,
  `id_satuan` int(11) UNSIGNED NULL DEFAULT NULL,
  `stok_saat_ini` int(11) NULL DEFAULT 0,
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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_barang
-- ----------------------------
INSERT INTO `tbl_barang` VALUES (4, 'P0001', 'Pulpen', 'habis_pakai', 1, 1, 100, NULL, '2025-08-25 09:51:01', '2025-08-25 09:51:01');

-- ----------------------------
-- Table structure for tbl_barang_keluar
-- ----------------------------
DROP TABLE IF EXISTS `tbl_barang_keluar`;
CREATE TABLE `tbl_barang_keluar`  (
  `id_barang_keluar` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_detail_permintaan` int(11) UNSIGNED NULL DEFAULT NULL,
  `jumlah_keluar` int(11) NULL DEFAULT NULL,
  `tanggal_keluar` date NULL DEFAULT NULL,
  `id_admin_gudang` int(11) UNSIGNED NULL DEFAULT NULL,
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
-- Table structure for tbl_detail_permintaan_atk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_detail_permintaan_atk`;
CREATE TABLE `tbl_detail_permintaan_atk`  (
  `id_detail_permintaan` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_permintaan` int(11) UNSIGNED NULL DEFAULT NULL,
  `id_barang` int(11) UNSIGNED NULL DEFAULT NULL,
  `nama_barang_custom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Nama barang jika item ini adalah permintaan pembelian baru',
  `jumlah_diminta` int(11) NULL DEFAULT NULL,
  `jumlah_disetujui` int(11) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail_permintaan`) USING BTREE,
  INDEX `id_permintaan`(`id_permintaan` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `tbl_detail_permintaan_atk_ibfk_1` FOREIGN KEY (`id_permintaan`) REFERENCES `tbl_permintaan_atk` (`id_permintaan`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `tbl_detail_permintaan_atk_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_detail_permintaan_atk
-- ----------------------------
INSERT INTO `tbl_detail_permintaan_atk` VALUES (1, 1, 4, NULL, 11, NULL, '2025-08-25 09:51:31');
INSERT INTO `tbl_detail_permintaan_atk` VALUES (2, 2, 4, NULL, 20, NULL, '2025-08-25 10:15:48');

-- ----------------------------
-- Table structure for tbl_kategori_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_kategori_barang`;
CREATE TABLE `tbl_kategori_barang`  (
  `id_kategori` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_kategori`) USING BTREE,
  UNIQUE INDEX `nama_kategori`(`nama_kategori` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_kategori_barang
-- ----------------------------
INSERT INTO `tbl_kategori_barang` VALUES (2, 'Alat Tulis');
INSERT INTO `tbl_kategori_barang` VALUES (1, 'Kertas');
INSERT INTO `tbl_kategori_barang` VALUES (3, 'Map & Arsip');
INSERT INTO `tbl_kategori_barang` VALUES (4, 'Tinta & Toner');

-- ----------------------------
-- Table structure for tbl_log_stok
-- ----------------------------
DROP TABLE IF EXISTS `tbl_log_stok`;
CREATE TABLE `tbl_log_stok`  (
  `id_log` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) UNSIGNED NULL DEFAULT NULL,
  `jenis_transaksi` enum('masuk','keluar','penyesuaian','dihapus') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jumlah_ubah` int(11) NULL DEFAULT NULL,
  `stok_sebelum` int(11) NULL DEFAULT NULL,
  `stok_sesudah` int(11) NULL DEFAULT NULL,
  `id_referensi` int(11) UNSIGNED NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_pengguna_aksi` int(11) UNSIGNED NULL DEFAULT NULL,
  `tanggal_log` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `tbl_log_stok_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_log_stok
-- ----------------------------
INSERT INTO `tbl_log_stok` VALUES (1, 4, 'penyesuaian', 100, 0, 100, NULL, 'Stok awal barang baru', NULL, '2025-08-25 09:51:01');

-- ----------------------------
-- Table structure for tbl_pemasok
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pemasok`;
CREATE TABLE `tbl_pemasok`  (
  `id_pemasok` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pemasok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemasok`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_pemasok
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_pengguna
-- ----------------------------
DROP TABLE IF EXISTS `tbl_pengguna`;
CREATE TABLE `tbl_pengguna`  (
  `id_pengguna` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_bagian` int(11) UNSIGNED NULL DEFAULT NULL,
  `role` enum('admin','pimpinan','pegawai','developer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Menambahkan peran developer untuk akses penuh',
  `is_active` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id_pengguna`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE,
  INDEX `id_bagian`(`id_bagian` ASC) USING BTREE,
  CONSTRAINT `tbl_pengguna_ibfk_1` FOREIGN KEY (`id_bagian`) REFERENCES `tbl_bagian` (`id_bagian`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_pengguna
-- ----------------------------
INSERT INTO `tbl_pengguna` VALUES (1, 'Administrator', 'admin', '$2y$10$9.p2aVot1e.Y.Pu0x01v5e.U.fA7w3.f5g.h6i.j7k.l8m.n9o', 3, 'admin', 1);

-- ----------------------------
-- Table structure for tbl_permintaan_atk
-- ----------------------------
DROP TABLE IF EXISTS `tbl_permintaan_atk`;
CREATE TABLE `tbl_permintaan_atk`  (
  `id_permintaan` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_permintaan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_pengguna_pemohon` int(11) UNSIGNED NULL DEFAULT NULL,
  `tanggal_permintaan` date NULL DEFAULT NULL,
  `status_permintaan` enum('Diajukan','Disetujui','Ditolak','Diproses Pembelian','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'Diajukan' COMMENT 'Menambahkan status untuk proses pembelian dan penyelesaian',
  `tipe_permintaan` enum('stok','pembelian') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'stok' COMMENT 'Membedakan antara permintaan stok internal dan permintaan pembelian baru',
  `catatan_pemohon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_pengguna_penyetuju` int(11) UNSIGNED NULL DEFAULT NULL,
  `tanggal_diproses` datetime NULL DEFAULT NULL,
  `catatan_penyetuju` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_operator_pembelian` int(11) UNSIGNED NULL DEFAULT NULL COMMENT 'ID pengguna (admin) yang memvalidasi pembelian',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_permintaan`) USING BTREE,
  UNIQUE INDEX `kode_permintaan`(`kode_permintaan` ASC) USING BTREE,
  INDEX `id_pengguna_pemohon`(`id_pengguna_pemohon` ASC) USING BTREE,
  INDEX `id_pengguna_penyetuju`(`id_pengguna_penyetuju` ASC) USING BTREE,
  INDEX `id_operator_pembelian`(`id_operator_pembelian` ASC) USING BTREE,
  CONSTRAINT `tbl_permintaan_atk_ibfk_1` FOREIGN KEY (`id_pengguna_pemohon`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tbl_permintaan_atk_ibfk_2` FOREIGN KEY (`id_pengguna_penyetuju`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tbl_permintaan_atk_ibfk_3` FOREIGN KEY (`id_operator_pembelian`) REFERENCES `tbl_pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_permintaan_atk
-- ----------------------------
INSERT INTO `tbl_permintaan_atk` VALUES (1, 'REQ-20250825-1756086706', 1, '2025-08-25', 'Diajukan', 'stok', 'test', NULL, NULL, NULL, NULL, '2025-08-25 09:51:31');
INSERT INTO `tbl_permintaan_atk` VALUES (2, 'REQ-20250825-1756088163', 1, '2025-08-25', 'Diajukan', 'pembelian', 'barang habis', NULL, NULL, NULL, NULL, '2025-08-25 10:15:48');

-- ----------------------------
-- Table structure for tbl_satuan_barang
-- ----------------------------
DROP TABLE IF EXISTS `tbl_satuan_barang`;
CREATE TABLE `tbl_satuan_barang`  (
  `id_satuan` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_satuan`) USING BTREE,
  UNIQUE INDEX `nama_satuan`(`nama_satuan` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_satuan_barang
-- ----------------------------
INSERT INTO `tbl_satuan_barang` VALUES (2, 'Box');
INSERT INTO `tbl_satuan_barang` VALUES (4, 'Lusin');
INSERT INTO `tbl_satuan_barang` VALUES (1, 'Pcs');
INSERT INTO `tbl_satuan_barang` VALUES (3, 'Rim');
INSERT INTO `tbl_satuan_barang` VALUES (5, 'Unit');

-- ----------------------------
-- View structure for v_permintaan_lengkap
-- ----------------------------
DROP VIEW IF EXISTS `v_permintaan_lengkap`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_permintaan_lengkap` AS SELECT 
    p.id_permintaan,
    p.kode_permintaan,
    p.tanggal_permintaan,
    p.tipe_permintaan, -- Kolom yang ditambahkan
    p.status_permintaan,
    p.catatan_pemohon,
    p.id_pengguna_pemohon,
    pemohon.nama_lengkap AS nama_pemohon,
    pemohon.id_bagian AS id_bagian_pemohon,
    p.id_pengguna_penyetuju,
    penyetuju.nama_lengkap AS nama_penyetuju,
    p.tanggal_diproses,
    p.catatan_penyetuju,
    (SELECT COUNT(*) FROM tbl_detail_permintaan_atk dp WHERE dp.id_permintaan = p.id_permintaan) AS jumlah_item
FROM 
    `tbl_permintaan_atk` p
JOIN 
    `tbl_pengguna` pemohon ON p.id_pengguna_pemohon = pemohon.id_pengguna
LEFT JOIN 
    `tbl_pengguna` penyetuju ON p.id_pengguna_penyetuju = penyetuju.id_pengguna ;

-- ----------------------------
-- Triggers structure for table tbl_barang
-- ----------------------------
DROP TRIGGER IF EXISTS `after_barang_insert`;
delimiter ;;
CREATE TRIGGER `after_barang_insert` AFTER INSERT ON `tbl_barang` FOR EACH ROW BEGIN
                INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan)
                VALUES (NEW.id_barang, 'penyesuaian', NEW.stok_saat_ini, 0, NEW.stok_saat_ini, 'Stok awal barang baru');
            END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table tbl_barang
-- ----------------------------
DROP TRIGGER IF EXISTS `after_barang_update`;
delimiter ;;
CREATE TRIGGER `after_barang_update` AFTER UPDATE ON `tbl_barang` FOR EACH ROW BEGIN
                IF OLD.stok_saat_ini <> NEW.stok_saat_ini THEN
                    INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, keterangan)
                    VALUES (NEW.id_barang, 'penyesuaian', (NEW.stok_saat_ini - OLD.stok_saat_ini), OLD.stok_saat_ini, NEW.stok_saat_ini, 'Penyesuaian stok manual');
                END IF;
            END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
