<?php

require_once APP_PATH . '/core/Model.php';

class Barang_model extends Model
{

    public function getAllActive()
    {

        $query = "
            SELECT 
                b.*, 
                k.nama_kategori, 
                s.nama_satuan,
                (b.stok_umum + b.stok_perkara) AS stok_total
            FROM tbl_barang b
            LEFT JOIN tbl_kategori_barang k ON b.id_kategori = k.id_kategori
            LEFT JOIN tbl_satuan_barang s ON b.id_satuan = s.id_satuan
            WHERE b.deleted_at IS NULL 
            ORDER BY b.nama_barang ASC
        ";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {

        $query = "
            SELECT 
                b.*, 
                k.nama_kategori, 
                s.nama_satuan,
                (b.stok_umum + b.stok_perkara) AS stok_total
            FROM tbl_barang b
            LEFT JOIN tbl_kategori_barang k ON b.id_kategori = k.id_kategori
            LEFT JOIN tbl_satuan_barang s ON b.id_satuan = s.id_satuan
            WHERE b.id_barang = ? AND b.deleted_at IS NULL
        ";
        $stmt  = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllKategori()
    {

        return $this->db->query("SELECT * FROM tbl_kategori_barang ORDER BY nama_kategori ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllSatuan()
    {

        return $this->db->query("SELECT * FROM tbl_satuan_barang ORDER BY nama_satuan ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data, $user_id)
    {

        $this->db->begin_transaction();
        try
        {
            $stmt = $this->db->prepare("INSERT INTO tbl_barang (kode_barang, nama_barang, jenis_barang, id_kategori, id_satuan, stok_umum, stok_perkara) VALUES (?, ?, ?, ?, ?, 0, 0)");
            $stmt->bind_param("sssii", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['id_kategori'], $data['id_satuan']);
            $stmt->execute();
            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Data barang berhasil ditambahkan.' ];
        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();
            log_query("INSERT INTO tbl_barang", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Error: " . $e->getMessage() : 'Gagal menambahkan data barang.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function update($id, $data, $user_id)
    {

        $this->db->begin_transaction();
        try
        {
            $stmt = $this->db->prepare("UPDATE tbl_barang SET kode_barang = ?, nama_barang = ?, jenis_barang = ?, id_kategori = ?, id_satuan = ? WHERE id_barang = ?");
            $stmt->bind_param("sssiii", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['id_kategori'], $data['id_satuan'], $id);
            $stmt->execute();
            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Data barang berhasil diperbarui.' ];
        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();
            log_query("UPDATE tbl_barang", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Error: " . $e->getMessage() : 'Gagal memperbarui data barang.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function softDelete($id, $user_id)
    {

        $this->db->begin_transaction();
        try
        {
            $stmt = $this->db->prepare("UPDATE tbl_barang SET deleted_at = NOW() WHERE id_barang = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Data barang berhasil dihapus.' ];
        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();
            log_query("UPDATE tbl_barang (soft delete)", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Error: " . $e->getMessage() : 'Gagal menghapus data barang.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

    public function getTrash()
    {

        $query = "
            SELECT b.*, k.nama_kategori, s.nama_satuan 
            FROM tbl_barang b
            LEFT JOIN tbl_kategori_barang k ON b.id_kategori = k.id_kategori
            LEFT JOIN tbl_satuan_barang s ON b.id_satuan = s.id_satuan
            WHERE b.deleted_at IS NOT NULL 
            ORDER BY b.deleted_at DESC
        ";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function restore($id, $user_id)
    {

        $this->db->begin_transaction();
        try
        {
            $stmt = $this->db->prepare("UPDATE tbl_barang SET deleted_at = NULL WHERE id_barang = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $this->db->commit();
            return [ 'success' => TRUE, 'message' => 'Data barang berhasil dipulihkan.' ];
        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();
            log_query("UPDATE tbl_barang (restore)", $e->getMessage());
            $msg = (ENVIRONMENT === 'development') ? "Error: " . $e->getMessage() : 'Gagal memulihkan data barang.';
            return [ 'success' => FALSE, 'message' => $msg ];
        }
    }

}