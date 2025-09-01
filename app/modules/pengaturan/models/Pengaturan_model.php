<?php

require_once APP_PATH . '/core/Model.php';

class Pengaturan_model extends Model
{

    public function clearAllTransactions()
    {

        $tables_to_truncate = [
            'tbl_log_stok',
            'tbl_barang_keluar',
            'tbl_detail_barang_masuk',
            'tbl_barang_masuk',
            'tbl_detail_permintaan_atk',
            'tbl_permintaan_atk',
        ];

        $this->db->begin_transaction();
        try
        {
            // Nonaktifkan foreign key check sementara
            $this->db->query("SET FOREIGN_KEY_CHECKS=0;");

            // Kosongkan semua tabel transaksi
            foreach ($tables_to_truncate as $table)
            {
                $this->db->query("TRUNCATE TABLE {$table}");
            }

            // Reset semua stok barang ke 0
            $this->db->query("UPDATE tbl_barang SET stok_umum = 0, stok_perkara = 0");

            // Aktifkan kembali foreign key check
            $this->db->query("SET FOREIGN_KEY_CHECKS=1;");

            $this->db->commit();

            return [ 'success' => TRUE, 'message' => 'Semua data transaksi berhasil dihapus dan stok telah di-reset.' ];
        } catch (Exception $e)
        {
            $this->db->rollback();
            log_query("Clear Transactions", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal menghapus data transaksi.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}
