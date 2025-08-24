<?php
require_once APP_PATH . '/core/Model.php';

class Barang_model extends Model {
    
    public function getAll() {
        $result = $this->db->query("SELECT * FROM tbl_barang ORDER BY nama_barang ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO tbl_barang (kode_barang, nama_barang, jenis_barang, stok_saat_ini, id_kategori, id_satuan) VALUES (?, ?, ?, ?, 1, 1)");
        $stmt->bind_param("sssi", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE tbl_barang SET kode_barang = ?, nama_barang = ?, jenis_barang = ?, stok_saat_ini = ? WHERE id_barang = ?");
        $stmt->bind_param("sssii", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tbl_barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}