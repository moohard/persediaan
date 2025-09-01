<?php

require_once APP_PATH . '/core/Model.php';

class Permintaan_model extends Model
{

    public function getAllRequests($user_id, $role)
    {

        $query = "
            SELECT 
                p.*, 
                u.nama_lengkap as nama_pemohon
            FROM tbl_permintaan_atk p
            JOIN tbl_pengguna u ON p.id_pengguna_pemohon = u.id_pengguna
        ";
        if ($role === 'pegawai')
        {
            $query .= " WHERE p.id_pengguna_pemohon = " . intval($user_id);
        }
        $query .= " ORDER BY p.tanggal_permintaan DESC, p.id_permintaan DESC";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getRequestDetailsById($id)
    {

        $data      = [];
        $stmt_main = $this->db->prepare("
            SELECT p.*, u.nama_lengkap as nama_pemohon, approver.nama_lengkap as nama_penyetuju
            FROM tbl_permintaan_atk p
            JOIN tbl_pengguna u ON p.id_pengguna_pemohon = u.id_pengguna
            LEFT JOIN tbl_pengguna approver ON p.id_pengguna_penyetuju = approver.id_pengguna
            WHERE p.id_permintaan = ?
        ");
        $stmt_main->bind_param('i', $id);
        $stmt_main->execute();
        $result_main = $stmt_main->get_result()->fetch_assoc();

        if ($result_main)
        {
            $data['main'] = $result_main;

            $stmt_items = $this->db->prepare("
                SELECT 
                    d.id_detail_permintaan,
                    d.jumlah_diminta,
                    d.jumlah_disetujui,
                    d.status_item,
                    COALESCE(b.nama_barang, d.nama_barang_custom) as nama_barang,
                    s.nama_satuan,
                    (b.stok_umum + b.stok_perkara) AS stok_total
                FROM tbl_detail_permintaan_atk d
                LEFT JOIN tbl_barang b ON d.id_barang = b.id_barang
                LEFT JOIN tbl_satuan_barang s ON b.id_satuan = s.id_satuan
                WHERE d.id_permintaan = ?
            ");
            $stmt_items->bind_param('i', $id);
            $stmt_items->execute();
            $data['items'] = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $data;
    }

    public function createRequest($data, $user_id, $tipe_permintaan)
    {

        $this->db->begin_transaction();
        try
        {
            $kode_permintaan = "REQ-" . date("Ymd") . "-" . strtoupper(uniqid());

            $stmt_req = $this->db->prepare("INSERT INTO tbl_permintaan_atk (kode_permintaan, id_pengguna_pemohon, tanggal_permintaan, catatan_pemohon, tipe_permintaan) VALUES (?, ?, CURDATE(), ?, ?)");
            $stmt_req->bind_param("siss", $kode_permintaan, $user_id, $data['catatan'], $tipe_permintaan);
            $stmt_req->execute();
            $id_permintaan = $this->db->insert_id;

            $stmt_detail = $this->db->prepare("INSERT INTO tbl_detail_permintaan_atk (id_permintaan, id_barang, nama_barang_custom, jumlah_diminta) VALUES (?, ?, ?, ?)");
            foreach ($data['items'] as $item)
            {
                $id_barang   = ($item['is_custom'] == FALSE) ? $item['id_barang'] : NULL;
                $nama_custom = ($item['is_custom'] == TRUE) ? $item['nama_barang'] : NULL;
                $jumlah      = intval($item['jumlah']);

                if ($jumlah <= 0) continue;

                $stmt_detail->bind_param("iisi", $id_permintaan, $id_barang, $nama_custom, $jumlah);
                $stmt_detail->execute();
            }

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Permintaan berhasil dibuat.' ];
        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();
            log_query("INSERT INTO tbl_permintaan_atk", $e->getMessage());
            $error_message = 'Terjadi kesalahan saat menyimpan data.';
            if (ENVIRONMENT === 'development')
            {
                $error_message .= " Pesan SQL: " . $e->getMessage();
            }
            return [ 'success' => FALSE, 'message' => $error_message ];
        }
    }

    public function approveRequest($id, $approver_id, $catatan, $items)
    {

        $this->db->begin_transaction();
        try
        {
            $stmt_check = $this->db->prepare("SELECT tipe_permintaan FROM tbl_permintaan_atk WHERE id_permintaan = ? AND status_permintaan = 'Diajukan'");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $permintaan = $stmt_check->get_result()->fetch_assoc();

            if (!$permintaan)
            {
                throw new Exception("Permintaan tidak ditemukan atau sudah diproses.");
            }
            $is_permintaan_stok = ($permintaan['tipe_permintaan'] == 'stok');
            $status_final       = $is_permintaan_stok ? 'Selesai' : 'Diproses Pembelian';

            foreach ($items as $item)
            {
                $detail_id        = intval($item['id']);
                $jumlah_disetujui = intval($item['jumlah']);

                $stmt_item_detail = $this->db->prepare("
                    SELECT 
                        d.id_barang, d.jumlah_diminta, b.stok_umum, b.stok_perkara,
                        COALESCE(b.nama_barang, d.nama_barang_custom) as nama_barang
                    FROM tbl_detail_permintaan_atk d
                    LEFT JOIN tbl_barang b ON d.id_barang = b.id_barang
                    WHERE d.id_detail_permintaan = ?
                ");
                $stmt_item_detail->bind_param("i", $detail_id);
                $stmt_item_detail->execute();
                $item_db = $stmt_item_detail->get_result()->fetch_assoc();

                if (!$item_db) throw new Exception("Detail item tidak ditemukan.");

                $stok_total_db = ($item_db['stok_umum'] ?? 0) + ($item_db['stok_perkara'] ?? 0);
                if ($jumlah_disetujui > $item_db['jumlah_diminta']) throw new Exception("Jumlah disetujui untuk '{$item_db['nama_barang']}' melebihi jumlah diminta.");

                if ($is_permintaan_stok && $item_db['id_barang'])
                {
                    if ($jumlah_disetujui > $stok_total_db)
                    {
                        throw new Exception("Jumlah disetujui untuk '{$item_db['nama_barang']}' melebihi stok total ({$stok_total_db}).");
                    }

                    if ($jumlah_disetujui > 0)
                    {
                        $stok_sebelum_umum    = $item_db['stok_umum'];
                        $stok_sebelum_perkara = $item_db['stok_perkara'];

                        $pengurangan_dari_umum    = min($stok_sebelum_umum, $jumlah_disetujui);
                        $sisa_pengurangan         = $jumlah_disetujui - $pengurangan_dari_umum;
                        $pengurangan_dari_perkara = min($stok_sebelum_perkara, $sisa_pengurangan);

                        $stmt_reduce_stock = $this->db->prepare("UPDATE tbl_barang SET stok_umum = stok_umum - ?, stok_perkara = stok_perkara - ? WHERE id_barang = ?");
                        $stmt_reduce_stock->bind_param("iii", $pengurangan_dari_umum, $pengurangan_dari_perkara, $item_db['id_barang']);
                        $stmt_reduce_stock->execute();

                        $stok_sesudah_umum    = $stok_sebelum_umum - $pengurangan_dari_umum;
                        $stok_sesudah_perkara = $stok_sebelum_perkara - $pengurangan_dari_perkara;
                        $keterangan_log       = "Pengeluaran stok untuk permintaan #" . $id;
                        $stmt_log             = $this->db->prepare("INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum_umum, stok_sesudah_umum, stok_sebelum_perkara, stok_sesudah_perkara, id_referensi, keterangan, id_pengguna_aksi) VALUES (?, 'keluar', ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt_log->bind_param(
                            "iiiiiiisi",
                            $item_db['id_barang'],
                            -$jumlah_disetujui,
                            $stok_sebelum_umum,
                            $stok_sesudah_umum,
                            $stok_sebelum_perkara,
                            $stok_sesudah_perkara,
                            $detail_id,
                            $keterangan_log,
                            $approver_id,
                        );
                        $stmt_log->execute();
                    }
                }

                $stmt_item_update = $this->db->prepare("UPDATE tbl_detail_permintaan_atk SET jumlah_disetujui = ? WHERE id_detail_permintaan = ?");
                $stmt_item_update->bind_param("ii", $jumlah_disetujui, $detail_id);
                $stmt_item_update->execute();
            }

            $stmt = $this->db->prepare("UPDATE tbl_permintaan_atk SET status_permintaan = ?, id_pengguna_penyetuju = ?, catatan_penyetuju = ?, tanggal_diproses = NOW() WHERE id_permintaan = ?");
            $stmt->bind_param("sisi", $status_final, $approver_id, $catatan, $id);
            $stmt->execute();

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Permintaan berhasil diproses.' ];
        } catch (Exception $e)
        {
            $this->db->rollback();
            log_query('UPDATE tbl_permintaan_atk (approve)', $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : $e->getMessage();
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function rejectRequest($id, $approver_id, $catatan)
    {

        $this->db->begin_transaction();
        try
        {
            $stmt = $this->db->prepare("UPDATE tbl_permintaan_atk SET status_permintaan = 'Ditolak', id_pengguna_penyetuju = ?, catatan_penyetuju = ?, tanggal_diproses = NOW() WHERE id_permintaan = ?");
            $stmt->bind_param("isi", $approver_id, $catatan, $id);
            $stmt->execute();

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Permintaan berhasil ditolak.' ];
        } catch (Exception $e)
        {
            $this->db->rollback();
            log_query('UPDATE tbl_permintaan_atk (reject)', $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal menolak permintaan.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}