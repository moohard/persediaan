<?php
require_once APP_PATH . '/core/Model.php';

class Barang_model extends Model {
    
    public function getAllActive() {
        $query = "SELECT * FROM tbl_barang WHERE deleted_at IS NULL ORDER BY nama_barang ASC";
        try {
            $result = $this->db->query($query);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            return [];
        }
    }

    public function getAllTrashed() {
        $query = "SELECT * FROM tbl_barang WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
        try {
            $result = $this->db->query($query);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM tbl_barang WHERE id_barang = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        $query = "INSERT INTO tbl_barang (kode_barang, nama_barang, jenis_barang, stok_saat_ini, id_kategori, id_satuan) VALUES (?, ?, ?, ?, 1, 1)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssi", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok']);
            $stmt->execute();
            return ['success' => true];
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Pesan SQL: " . $e->getMessage() : 'Gagal menyimpan data.';
            return ['success' => false, 'message' => $msg];
        }
    }

    public function update($id, $data) {
        $query = "UPDATE tbl_barang SET kode_barang = ?, nama_barang = ?, jenis_barang = ?, stok_saat_ini = ? WHERE id_barang = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssii", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok'], $id);
            $stmt->execute();
            return ['success' => true];
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Pesan SQL: " . $e->getMessage() : 'Gagal memperbarui data.';
            return ['success' => false, 'message' => $msg];
        }
    }

    public function softDelete($id) {
        $query = "UPDATE tbl_barang SET deleted_at = NOW() WHERE id_barang = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return ['success' => true];
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Pesan SQL: " . $e->getMessage() : 'Gagal menghapus data.';
            return ['success' => false, 'message' => $msg];
        }
    }

    public function restore($id) {
        $query = "UPDATE tbl_barang SET deleted_at = NULL WHERE id_barang = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            log_query($query, $e->getMessage());
            return false;
        }
    }
}