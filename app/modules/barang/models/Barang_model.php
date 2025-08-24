<?php

require_once APP_PATH . '/core/Model.php';

class Barang_model extends Model
{

    public function getAllActive()
    {

        $query = "SELECT * FROM tbl_barang WHERE deleted_at IS NULL ORDER BY nama_barang ASC";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllTrashed()
    {

        $query = "SELECT * FROM tbl_barang WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {

        $stmt = $this->db->prepare("SELECT * FROM tbl_barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {

        try
        {
            $stmt = $this->db->prepare("INSERT INTO tbl_barang (kode_barang, nama_barang, jenis_barang, stok_saat_ini, id_kategori, id_satuan) VALUES (?, ?, ?, ?, 1, 1)");
            $stmt->bind_param("sssi", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok']);
            $stmt->execute();
            return [ 'success' => TRUE ];
        } catch (mysqli_sql_exception $e)
        {
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal menyimpan data.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function update($id, $data)
    {

        try
        {
            $stmt = $this->db->prepare("UPDATE tbl_barang SET kode_barang = ?, nama_barang = ?, jenis_barang = ?, stok_saat_ini = ? WHERE id_barang = ?");
            $stmt->bind_param("sssii", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok'], $id);
            $stmt->execute();
            return [ 'success' => TRUE ];
        } catch (mysqli_sql_exception $e)
        {
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal memperbarui data.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function softDelete($id)
    {

        try
        {
            $stmt = $this->db->prepare("UPDATE tbl_barang SET deleted_at = NOW() WHERE id_barang = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return [ 'success' => TRUE ];
        } catch (mysqli_sql_exception $e)
        {
            $msg = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Gagal menghapus data.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function restore($id)
    {

        $stmt = $this->db->prepare("UPDATE tbl_barang SET deleted_at = NULL WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}