<?php

require_once APP_PATH . '/core/Model.php';

class Barangmasuk_model extends Model
{

    public function getProcessedPurchaseRequests()
    {

        $query = "
            SELECT p.*, u.nama_lengkap as nama_pemohon,
            (SELECT COUNT(*) FROM tbl_detail_permintaan_atk WHERE id_permintaan = p.id_permintaan) as jumlah_item
            FROM tbl_permintaan_atk p
            JOIN tbl_pengguna u ON p.id_pengguna_pemohon = u.id_pengguna
            WHERE p.tipe_permintaan = 'pembelian' AND p.status_permintaan = 'Sudah Dibeli'
            ORDER BY p.tanggal_permintaan DESC
        ";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getRequestDetailsForReceipt($id)
    {

        $stmt = $this->db->prepare("
            SELECT 
                d.id_detail_permintaan,
                d.jumlah_disetujui,
                d.nama_barang_custom,
                d.id_barang,
                COALESCE(b.nama_barang, d.nama_barang_custom) as nama_barang
            FROM tbl_detail_permintaan_atk d
            LEFT JOIN tbl_barang b ON d.id_barang = b.id_barang
            WHERE d.id_permintaan = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function processReceipt($id_permintaan, $user_id, $itemsAlokasi)
    {

        $this->db->begin_transaction();
        try
        {
            $no_transaksi = "BM-" . date("Ymd") . "-" . strtoupper(uniqid());
            $stmt_bm      = $this->db->prepare("INSERT INTO tbl_barang_masuk (no_transaksi_masuk, id_pemasok, tanggal_masuk, id_pengguna_penerima, id_permintaan_terkait) VALUES (?, 1, CURDATE(), ?, ?)");
            $stmt_bm->bind_param("sii", $no_transaksi, $user_id, $id_permintaan);
            $stmt_bm->execute();
            $id_barang_masuk = $this->db->insert_id;

            foreach ($itemsAlokasi as $alokasi)
            {
                $detail_permintaan_id = intval($alokasi['id_detail_permintaan']);
                $id_barang_final      = intval($alokasi['id_barang']) ?: NULL;
                $nama_barang_custom   = $alokasi['nama_barang_custom'];
                $jumlah_diterima      = intval($alokasi['jumlah_diterima']);

                if ($jumlah_diterima <= 0) continue;

                if (is_null($id_barang_final) && !empty($nama_barang_custom))
                {
                    // Ini barang baru, tambahkan ke tbl_barang
                    $stmt_new_item = $this->db->prepare("INSERT INTO tbl_barang (kode_barang, nama_barang, jenis_barang, id_kategori, id_satuan) VALUES (?, ?, 'habis_pakai', 1, 1)");
                    $kode_baru     = "BRG-NEW-" . strtoupper(uniqid());
                    $stmt_new_item->bind_param("ss", $kode_baru, $nama_barang_custom);
                    $stmt_new_item->execute();
                    $id_barang_final = $this->db->insert_id;

                    // Update detail permintaan dengan id barang yang baru dibuat
                    $stmt_update_detail = $this->db->prepare("UPDATE tbl_detail_permintaan_atk SET id_barang = ? WHERE id_detail_permintaan = ?");
                    $stmt_update_detail->bind_param("ii", $id_barang_final, $detail_permintaan_id);
                    $stmt_update_detail->execute();
                }

                $stmt_dbm = $this->db->prepare("INSERT INTO tbl_detail_barang_masuk (id_barang_masuk, id_barang, jumlah_diterima, jumlah_umum, jumlah_perkara) VALUES (?, ?, ?, ?, ?)");
                $stmt_dbm->bind_param("iiiii", $id_barang_masuk, $id_barang_final, $jumlah_diterima, $alokasi['jumlah_umum'], $alokasi['jumlah_perkara']);
                $stmt_dbm->execute();
                $id_detail_masuk = $this->db->insert_id;

                if ($id_barang_final)
                {
                    // Dapatkan stok sebelum diubah untuk logging
                    $stmt_get_stok = $this->db->prepare("SELECT (stok_umum + stok_perkara) as total FROM tbl_barang WHERE id_barang = ?");
                    $stmt_get_stok->bind_param("i", $id_barang_final);
                    $stmt_get_stok->execute();
                    $stok_sebelum = $stmt_get_stok->get_result()->fetch_assoc()['total'] ?? 0;

                    // Update stok
                    $stmt_update_stok = $this->db->prepare("UPDATE tbl_barang SET stok_umum = stok_umum + ?, stok_perkara = stok_perkara + ? WHERE id_barang = ?");
                    $stmt_update_stok->bind_param("iii", $alokasi['jumlah_umum'], $alokasi['jumlah_perkara'], $id_barang_final);
                    $stmt_update_stok->execute();

                    // **[PERBAIKAN] Tambahkan Log Stok Masuk**
                    $stok_sesudah   = $stok_sebelum + $jumlah_diterima;
                    $keterangan_log = "Penerimaan barang dari permintaan #" . $id_permintaan;
                    $stmt_log       = $this->db->prepare("INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum_total, stok_sesudah_total, id_referensi, keterangan, id_pengguna_aksi) VALUES (?, 'masuk', ?, ?, ?, ?, ?, ?)");
                    $stmt_log->bind_param("iiiiisi", $id_barang_final, $jumlah_diterima, $stok_sebelum, $stok_sesudah, $id_detail_masuk, $keterangan_log, $user_id);
                    $stmt_log->execute();
                }

                // Tandai item permintaan sebagai sudah diterima
                $stmt_update_item_status = $this->db->prepare("UPDATE tbl_detail_permintaan_atk SET status_item = 'Selesai Diterima' WHERE id_detail_permintaan = ?");
                $stmt_update_item_status->bind_param("i", $detail_permintaan_id);
                $stmt_update_item_status->execute();
            }

            // Cek jika semua item sudah diterima, update status permintaan utama
            $stmt_check_all = $this->db->prepare("SELECT COUNT(*) as total FROM tbl_detail_permintaan_atk WHERE id_permintaan = ? AND status_item IS NULL");
            $stmt_check_all->bind_param("i", $id_permintaan);
            $stmt_check_all->execute();
            $sisa_item = $stmt_check_all->get_result()->fetch_assoc()['total'];

            if ($sisa_item == 0)
            {
                $stmt_update_req = $this->db->prepare("UPDATE tbl_permintaan_atk SET status_permintaan = 'Selesai' WHERE id_permintaan = ?");
                $stmt_update_req->bind_param("i", $id_permintaan);
                $stmt_update_req->execute();
            }

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Penerimaan barang berhasil diproses.' ];
        } catch (Exception $e)
        {
            $this->db->rollback();
            log_query("Proses Penerimaan", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Terjadi kesalahan saat memproses penerimaan.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}