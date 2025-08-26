<?php
require_once APP_PATH . '/core/Model.php';

class Pembelian_model extends Model {
    
    // Mengambil semua permintaan pembelian yang sudah disetujui pimpinan
    public function getApprovedPurchaseRequests() {
        $query = "
            SELECT * FROM v_permintaan_lengkap 
            WHERE tipe_permintaan = 'pembelian' AND status_permintaan = 'Disetujui'
            ORDER BY tanggal_diproses ASC
        ";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    // Memvalidasi pembelian dan mengubah statusnya
    public function validatePurchase($id_permintaan, $id_operator) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE tbl_permintaan_atk 
                 SET status_permintaan = 'Diproses Pembelian', id_operator_pembelian = ? 
                 WHERE id_permintaan = ? AND status_permintaan = 'Disetujui' AND tipe_permintaan = 'pembelian'"
            );
            $stmt->bind_param("ii", $id_operator, $id_permintaan);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception("Permintaan tidak ditemukan atau statusnya tidak valid untuk divalidasi.");
            }
            
            return ['success' => true];
        } catch (mysqli_sql_exception $e) {
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal memvalidasi pembelian.';
            return ['success' => false, 'message' => $msg];
        }
    }
}