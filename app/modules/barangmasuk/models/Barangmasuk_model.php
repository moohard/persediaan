<?php

require_once APP_PATH . '/core/Model.php';

class Barangmasuk_model extends Model
{

    public function getPurchasedRequests()
    {

        $query = "
            SELECT * FROM v_permintaan_lengkap 
            WHERE tipe_permintaan = 'pembelian' AND status_permintaan = 'Diproses Pembelian'
            ORDER BY tanggal_diproses ASC
        ";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function processGoodsReceipt($id_permintaan, $id_admin)
    {

        $this->db->begin_transaction();
        try
        {
            // 1. Ambil semua item dari permintaan ini
            $detail_stmt = $this->db->prepare("SELECT * FROM tbl_detail_permintaan_atk WHERE id_permintaan = ?");
            $detail_stmt->bind_param("i", $id_permintaan);
            $detail_stmt->execute();
            $items = $detail_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // 2. Buat header barang masuk
            $barang_masuk_stmt = $this->db->prepare("INSERT INTO tbl_barang_masuk (no_transaksi_masuk, id_pemasok, tanggal_masuk, id_pengguna_penerima, keterangan) VALUES (?, 1, ?, ?, ?)");
            $kode_transaksi    = 'BM-' . date('YmdHis');
            $tanggal_masuk     = date('Y-m-d');
            $keterangan        = "Penerimaan dari permintaan pembelian #$id_permintaan";
            $barang_masuk_stmt->bind_param("ssis", $kode_transaksi, $tanggal_masuk, $id_admin, $keterangan);
            $barang_masuk_stmt->execute();
            $id_barang_masuk = $this->db->insert_id;

            // 3. Proses setiap item
            foreach ($items as $item)
            {
                $id_barang = $item['id_barang'];
                // Jika ini item baru, buat dulu di tbl_barang
                if ($item['id_barang'] === NULL)
                {
                    $new_barang_stmt = $this->db->prepare("INSERT INTO tbl_barang (kode_barang, nama_barang, id_kategori, id_satuan) VALUES (?, ?, 1, 1)");
                    $kode_baru       = 'BRG-' . time();
                    $new_barang_stmt->bind_param("ss", $kode_baru, $item['nama_barang_custom']);
                    $new_barang_stmt->execute();
                    $id_barang = $this->db->insert_id;
                }

                // Tambahkan ke detail barang masuk
                $detail_masuk_stmt = $this->db->prepare("INSERT INTO tbl_detail_barang_masuk (id_barang_masuk, id_barang, jumlah_masuk) VALUES (?, ?, ?)");
                $detail_masuk_stmt->bind_param("iii", $id_barang_masuk, $id_barang, $item['jumlah_disetujui']);
                $detail_masuk_stmt->execute();
            }

            // 4. Update status permintaan menjadi "Selesai"
            $update_permintaan_stmt = $this->db->prepare("UPDATE tbl_permintaan_atk SET status_permintaan = 'Selesai' WHERE id_permintaan = ?");
            $update_permintaan_stmt->bind_param("i", $id_permintaan);
            $update_permintaan_stmt->execute();

            $this->db->commit();
            return [ 'success' => TRUE ];
        } catch (Exception $e)
        {
            $this->db->rollback();
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal memproses penerimaan barang.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}