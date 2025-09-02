<?php

require_once APP_PATH . '/core/Model.php';

class Stockopname_model extends Model
{

    public function getHistory()
    {

        $query = "
            SELECT so.*, u.nama_lengkap as nama_penanggung_jawab
            FROM tbl_stock_opname so
            JOIN tbl_pengguna u ON so.id_pengguna_penanggung_jawab = u.id_pengguna
            ORDER BY so.tanggal_opname DESC, so.id_opname DESC
        ";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getLatestStockData()
    {

        $query = "
            SELECT id_barang, kode_barang, nama_barang, stok_umum, stok_perkara 
            FROM tbl_barang WHERE deleted_at IS NULL ORDER BY nama_barang ASC
        ";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function saveOpname($keterangan, $items, $user_id)
    {

        if (empty($items))
        {
            return [ 'success' => FALSE, 'message' => 'Tidak ada data barang yang diproses.' ];
        }

        $this->db->begin_transaction();
        try
        {
            // 1. Buat record master stock opname
            $kode_opname = "OPN-" . date("Ymd-His");
            $stmt_opname = $this->db->prepare("INSERT INTO tbl_stock_opname (kode_opname, tanggal_opname, id_pengguna_penanggung_jawab, keterangan) VALUES (?, CURDATE(), ?, ?)");
            $stmt_opname->bind_param('sis', $kode_opname, $user_id, $keterangan);
            $stmt_opname->execute();
            $id_opname = $this->db->insert_id;

            // 2. Proses setiap item
            foreach ($items as $item)
            {
                $id_barang           = (int) $item['id_barang'];
                $stok_sistem_umum    = (int) $item['stok_sistem_umum'];
                $stok_sistem_perkara = (int) $item['stok_sistem_perkara'];
                $stok_fisik_umum     = (int) $item['stok_fisik_umum'];
                $stok_fisik_perkara  = (int) $item['stok_fisik_perkara'];

                // Jika ada selisih, update stok di tbl_barang
                if (($stok_sistem_umum !== $stok_fisik_umum) || ($stok_sistem_perkara !== $stok_fisik_perkara))
                {
                    $stmt_update = $this->db->prepare("UPDATE tbl_barang SET stok_umum = ?, stok_perkara = ? WHERE id_barang = ?");
                    $stmt_update->bind_param("iii", $stok_fisik_umum, $stok_fisik_perkara, $id_barang);
                    $stmt_update->execute();
                }

                // 4. [DIUBAH] Catat di log stok dengan skema baru
                $jumlah_ubah_total = ($stok_fisik_umum + $stok_fisik_perkara) - ($stok_sistem_umum + $stok_sistem_perkara);
                $log_keterangan    = "Stock Opname: {$kode_opname}.";

                $stmt_log = $this->db->prepare(
                    "INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum_umum, stok_sesudah_umum, stok_sebelum_perkara, stok_sesudah_perkara, id_referensi, keterangan, id_pengguna_aksi) 
                     VALUES (?, 'penyesuaian', ?, ?, ?, ?, ?, ?, ?, ?)",
                );
                $stmt_log->bind_param(
                    "iiiiiiisi",
                    $id_barang,
                    $jumlah_ubah_total,
                    $stok_sistem_umum,
                    $stok_fisik_umum,
                    $stok_sistem_perkara,
                    $stok_fisik_perkara,
                    $id_opname,
                    $log_keterangan,
                    $user_id,
                );
                $stmt_log->execute();
            }

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Stock opname berhasil disimpan.' ];

        } catch (Exception $e)
        {
            $this->db->rollback();
            log_query("Save Stock Opname", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal menyimpan data stock opname.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}
