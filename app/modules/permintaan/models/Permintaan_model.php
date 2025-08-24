<?php

require_once APP_PATH . '/core/Model.php';

class Permintaan_model extends Model
{

    public function getAllPermintaan()
    {

        // Query ini menggabungkan beberapa tabel untuk mendapatkan informasi lengkap
        $query  = "
            SELECT 
                p.id_permintaan,
                p.kode_permintaan,
                p.tanggal_permintaan,
                p.status_permintaan,
                pemohon.nama_lengkap AS nama_pemohon,
                (SELECT COUNT(*) FROM tbl_detail_permintaan_atk dp WHERE dp.id_permintaan = p.id_permintaan) AS jumlah_item
            FROM tbl_permintaan_atk p
            JOIN tbl_pengguna pemohon ON p.id_pengguna_pemohon = pemohon.id_pengguna
            ORDER BY p.tanggal_permintaan DESC, p.id_permintaan DESC
        ";
        $result = $this->db->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createPermintaan($catatan, $items, $id_pemohon)
    {

        // Gunakan transaksi untuk memastikan semua query berhasil atau semua gagal
        $this->db->begin_transaction();

        try
        {
            // 1. Buat header permintaan
            $kode_permintaan    = 'REQ-' . date('Ymd') . '-' . time();
            $tanggal_permintaan = date('Y-m-d');

            $stmt_header = $this->db->prepare(
                "INSERT INTO tbl_permintaan_atk (kode_permintaan, id_pengguna_pemohon, tanggal_permintaan, catatan_pemohon) VALUES (?, ?, ?, ?)",
            );
            $stmt_header->bind_param("siss", $kode_permintaan, $id_pemohon, $tanggal_permintaan, $catatan);
            $stmt_header->execute();

            $id_permintaan_baru = $this->db->insert_id;
            if ($id_permintaan_baru === 0)
            {
                throw new Exception("Gagal mendapatkan ID permintaan baru.");
            }

            // 2. Masukkan setiap item ke detail permintaan
            $stmt_detail = $this->db->prepare(
                "INSERT INTO tbl_detail_permintaan_atk (id_permintaan, id_barang, jumlah_diminta) VALUES (?, ?, ?)",
            );
            foreach ($items as $item)
            {
                $stmt_detail->bind_param("iii", $id_permintaan_baru, $item['id_barang'], $item['jumlah']);
                $stmt_detail->execute();
            }

            // Jika semua berhasil, commit transaksi
            $this->db->commit();
            return [ 'success' => TRUE ];

        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();

            $error_message = 'Terjadi kesalahan saat menyimpan data.';
            // PERBAIKAN: Berikan pesan error yang lebih detail di mode development
            if (ENVIRONMENT === 'development')
            {
                $error_message .= " Pesan SQL: " . $e->getMessage();
            }

            return [ 'success' => FALSE, 'message' => $error_message ];
        }
    }

}