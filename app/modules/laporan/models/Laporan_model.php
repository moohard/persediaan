<?php

require_once APP_PATH . '/core/Model.php';

class Laporan_model extends Model
{

    public function getAllStock()
    {

        $query = "
            SELECT 
                b.kode_barang,
                b.nama_barang,
                k.nama_kategori,
                s.nama_satuan,
                b.stok_umum,
                b.stok_perkara,
                (b.stok_umum + b.stok_perkara) as stok_total
            FROM tbl_barang b
            LEFT JOIN tbl_kategori_barang k ON b.id_kategori = k.id_kategori
            LEFT JOIN tbl_satuan_barang s ON b.id_satuan = s.id_satuan
            WHERE b.deleted_at IS NULL
            ORDER BY b.nama_barang ASC
        ";

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getStockCard($id_barang)
    {

        if (empty($id_barang))
        {
            return [];
        }
        $stmt = $this->db->prepare("
            SELECT l.*, u.nama_lengkap as nama_pengguna
            FROM tbl_log_stok l
            LEFT JOIN tbl_pengguna u ON l.id_pengguna_aksi = u.id_pengguna
            WHERE l.id_barang = ?
            ORDER BY l.tanggal_log DESC
        ");
        $stmt->bind_param('i', $id_barang);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function applyDateFilter($query, $filters)
    {

        $params = [];
        $types  = '';
        if (!empty($filters['start_date']))
        {
            $query .= " AND p.tanggal_permintaan >= ?";
            $params[] = $filters['start_date'];
            $types .= 's';
        }
        if (!empty($filters['end_date']))
        {
            $query .= " AND p.tanggal_permintaan <= ?";
            $params[] = $filters['end_date'];
            $types .= 's';
        }
        return [ 'query' => $query, 'params' => $params, 'types' => $types ];
    }

    public function getPermintaanReport($filters)
    {

        $query = "SELECT * FROM v_permintaan_lengkap p WHERE 1=1";

        if (!empty($filters['status']) && $filters['status'] !== 'semua')
        {
            $query .= " AND p.status_permintaan = '" . $this->db->real_escape_string($filters['status']) . "'";
        }

        $dateFilter = $this->applyDateFilter($query, $filters);
        $query      = $dateFilter['query'];

        $query .= " ORDER BY p.tanggal_permintaan DESC";

        $stmt = $this->db->prepare($query);
        if (!empty($dateFilter['params']))
        {
            $stmt->bind_param($dateFilter['types'], ...$dateFilter['params']);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPembelianReport($filters)
    {

        $query = "SELECT * FROM v_permintaan_lengkap p WHERE p.tipe_permintaan = 'pembelian'";

        if (!empty($filters['status']) && $filters['status'] !== 'semua')
        {
            $query .= " AND p.status_permintaan = '" . $this->db->real_escape_string($filters['status']) . "'";
        }

        $dateFilter = $this->applyDateFilter($query, $filters);
        $query      = $dateFilter['query'];

        $query .= " ORDER BY p.tanggal_permintaan DESC";

        $stmt = $this->db->prepare($query);
        if (!empty($dateFilter['params']))
        {
            $stmt->bind_param($dateFilter['types'], ...$dateFilter['params']);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

}