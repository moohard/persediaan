<?php

require_once APP_PATH . '/core/Model.php';

class Permintaan_model extends Model
{

    public function getAllPermintaan()
    {

        $query = "SELECT * FROM v_permintaan_lengkap ORDER BY tanggal_permintaan DESC, id_permintaan DESC";
        try
        {
            $result = $this->db->query($query);

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e)
        {
            log_query($query, $e->getMessage());
            return [];
        }
    }

    public function createPermintaan($catatan, $items, $id_pemohon)
    {

        $this->db->begin_transaction();
        try
        {
            $kode_permintaan    = 'REQ-' . date('Ymd') . '-' . time();
            $tanggal_permintaan = date('Y-m-d');

            $stmt_header = $this->db->prepare(
                "INSERT INTO tbl_permintaan_atk (kode_permintaan, id_pengguna_pemohon, tanggal_permintaan, catatan_pemohon) VALUES (?, ?, ?, ?)",
            );
            $stmt_header->bind_param("siss", $kode_permintaan, $id_pemohon, $tanggal_permintaan, $catatan);
            $stmt_header->execute();

            $id_permintaan_baru = $this->db->insert_id;
            if ($id_permintaan_baru === 0)
            {
                throw new Exception("Gagal mendapatkan ID permintaan baru.");
            }

            $stmt_detail = $this->db->prepare(
                "INSERT INTO tbl_detail_permintaan_atk (id_permintaan, id_barang, jumlah_diminta) VALUES (?, ?, ?)",
            );
            foreach ($items as $item)
            {
                $stmt_detail->bind_param("iii", $id_permintaan_baru, $item['id_barang'], $item['jumlah']);
                $stmt_detail->execute();
            }

            $this->db->commit();
            return [ 'success' => TRUE ];

        } catch (mysqli_sql_exception $e)
        {
            $this->db->rollback();
            $error_message = 'Terjadi kesalahan saat menyimpan data.';
            if (ENVIRONMENT === 'development')
            {
                $error_message .= " Pesan SQL: " . $e->getMessage();
            }
            return [ 'success' => FALSE, 'message' => $error_message ];
        }
    }

}