<?php
require_once APP_PATH . '/core/Model.php';

class Permintaan_model extends Model {
    
    public function getAllPermintaan() {
        $query = "SELECT * FROM v_permintaan_lengkap ORDER BY tanggal_permintaan DESC, id_permintaan DESC";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDetailById($id) {
        $data = [];
        $stmt_header = $this->db->prepare("SELECT * FROM v_permintaan_lengkap WHERE id_permintaan = ?");
        $stmt_header->bind_param("i", $id);
        $stmt_header->execute();
        $data['header'] = $stmt_header->get_result()->fetch_assoc();

        $stmt_detail = $this->db->prepare("
            SELECT dp.*, b.nama_barang, b.stok_saat_ini 
            FROM tbl_detail_permintaan_atk dp
            LEFT JOIN tbl_barang b ON dp.id_barang = b.id_barang
            WHERE dp.id_permintaan = ?
        ");
        $stmt_detail->bind_param("i", $id);
        $stmt_detail->execute();
        $data['items'] = $stmt_detail->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return $data;
    }

    public function createPermintaan($catatan, $items, $id_pemohon, $tipe_permintaan) {
        $this->db->begin_transaction();
        try {
            $kode_permintaan = 'REQ-' . date('Ymd') . '-' . time();
            $tanggal_permintaan = date('Y-m-d');
            
            $stmt_header = $this->db->prepare(
                "INSERT INTO tbl_permintaan_atk (kode_permintaan, id_pengguna_pemohon, tanggal_permintaan, tipe_permintaan, catatan_pemohon) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt_header->bind_param("sisss", $kode_permintaan, $id_pemohon, $tanggal_permintaan, $tipe_permintaan, $catatan);
            $stmt_header->execute();

            $id_permintaan_baru = $this->db->insert_id;
            if ($id_permintaan_baru === 0) throw new Exception("Gagal membuat header permintaan.");

            $stmt_detail = $this->db->prepare(
                "INSERT INTO tbl_detail_permintaan_atk (id_permintaan, id_barang, nama_barang_custom, jumlah_diminta) VALUES (?, ?, ?, ?)"
            );
            foreach ($items as $item) {
                $id_barang = $item['is_custom'] ? null : $item['id_barang'];
                $nama_custom = $item['is_custom'] ? $item['nama_barang_custom'] : null;
                $stmt_detail->bind_param("iisi", $id_permintaan_baru, $id_barang, $nama_custom, $item['jumlah']);
                $stmt_detail->execute();
            }

            $this->db->commit();
            return ['success' => true];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Terjadi kesalahan saat menyimpan data.';
            return ['success' => false, 'message' => $msg];
        }
    }

    public function processPermintaan($id, $action, $catatan, $id_penyetuju, $items = []) {
        $this->db->begin_transaction();
        try {
            $new_status = ($action === 'approve') ? 'Disetujui' : 'Ditolak';
            
            $stmt = $this->db->prepare(
                "UPDATE tbl_permintaan_atk SET status_permintaan = ?, id_pengguna_penyetuju = ?, tanggal_diproses = NOW(), catatan_penyetuju = ? WHERE id_permintaan = ? AND status_permintaan = 'Diajukan'"
            );
            $stmt->bind_param("sisi", $new_status, $id_penyetuju, $catatan, $id);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception("Permintaan tidak ditemukan atau sudah diproses.");
            }

            if ($action === 'approve') {
                $stmt_approve = $this->db->prepare(
                    "UPDATE tbl_detail_permintaan_atk SET jumlah_disetujui = ? WHERE id_detail_permintaan = ?"
                );
                foreach ($items as $item) {
                    $stmt_approve->bind_param("ii", $item['jumlah_disetujui'], $item['id_detail']);
                    $stmt_approve->execute();
                }
            }

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollback();
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal memproses permintaan.';
            return ['success' => false, 'message' => $msg];
        }
    }
}