<?php
require_once APP_PATH . '/core/Model.php';

class Stok_model extends Model {
    public function getLogByBarangId($id_barang) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_log_stok WHERE id_barang = ? ORDER BY tanggal_log DESC");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>