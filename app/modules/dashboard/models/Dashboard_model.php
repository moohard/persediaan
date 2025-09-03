<?php

require_once APP_PATH . '/core/Model.php';

class Dashboard_model extends Model
{

    public function getSummaryStats()
    {

        $stats = [];

        // Total Jenis Barang Aktif
        $result1               = $this->db->query("SELECT COUNT(id_barang) as total FROM tbl_barang WHERE deleted_at IS NULL");
        $stats['total_barang'] = $result1->fetch_assoc()['total'] ?? 0;

        // Permintaan Bulan Ini
        $result2                       = $this->db->query("SELECT COUNT(id_permintaan) as total FROM tbl_permintaan_atk WHERE MONTH(tanggal_permintaan) = MONTH(CURDATE()) AND YEAR(tanggal_permintaan) = YEAR(CURDATE())");
        $stats['permintaan_bulan_ini'] = $result2->fetch_assoc()['total'] ?? 0;

        // Stok Kritis (contoh: stok di bawah 5)
        $result3              = $this->db->query("SELECT COUNT(id_barang) as total FROM tbl_barang WHERE (stok_umum + stok_perkara) < 5 AND deleted_at IS NULL");
        $stats['stok_kritis'] = $result3->fetch_assoc()['total'] ?? 0;

        return $stats;
    }

    public function getMonthlyUsageChartData()
    {

        // Query untuk mengambil total barang keluar per bulan dalam 6 bulan terakhir
        $query  = "
            SELECT 
                DATE_FORMAT(tanggal_keluar, '%Y-%m') AS bulan,
                SUM(jumlah_keluar) AS total
            FROM tbl_barang_keluar
            WHERE tanggal_keluar >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(tanggal_keluar, '%Y-%m')
            ORDER BY bulan ASC;
        ";
        $result = $this->db->query($query);

        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Format data agar sesuai dengan Chart.js
        $labels = [];
        $values = [];
        // Inisialisasi 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--)
        {
            $month    = date('Y-m', strtotime("-$i months"));
            $labels[] = date('M Y', strtotime("-$i months"));

            // Cari data untuk bulan ini
            $found = FALSE;
            foreach ($data as $row)
            {
                if ($row['bulan'] == $month)
                {
                    $values[] = (int) $row['total'];
                    $found    = TRUE;
                    break;
                }
            }
            if (!$found)
            {
                $values[] = 0;
            }
        }

        return [ 'labels' => $labels, 'values' => $values ];
    }

}