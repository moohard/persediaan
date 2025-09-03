<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ResetAllIds extends AbstractMigration
{
    public function up(): void
    {
        // Nonaktifkan foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');

        // Reset auto increment untuk semua tabel
        $tables = [
            'tbl_bagian',
            'tbl_barang',
            'tbl_barang_keluar',
            'tbl_barang_masuk',
            'tbl_detail_barang_masuk',
            'tbl_detail_permintaan_atk',
            'tbl_detail_stock_opname',
            'tbl_kategori_barang',
            'tbl_log_stok',
            'tbl_notifikasi',
            'tbl_pemasok',
            'tbl_pengaturan',
            'tbl_pengguna',
            'tbl_permintaan_atk',
            'tbl_permissions',
            'tbl_role_permissions',
            'tbl_roles',
            'tbl_satuan_barang',
            'tbl_stock_opname',
            'phinxlog'
        ];

        foreach ($tables as $table) {
            $this->execute("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        }

        // Aktifkan kembali foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void
    {
        // Tidak ada operasi untuk down migration
    }
}