<?php

class Model
{

    protected $db;

    public function __construct()
    {

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try
        {
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $this->db->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e)
        {
            // Panggil fungsi log sebelum menghentikan eksekusi
            log_query("Koneksi Gagal", $e->getMessage());
            if (ENVIRONMENT === 'development')
            {
                die("Koneksi database gagal: " . $e->getMessage());
            } else
            {
                die("Tidak dapat terhubung ke server database.");
            }
        }
    }

    // PERBAIKAN: Tambahkan fungsi untuk logging sebelum query dieksekusi
    protected function logQueryBeforeExecute($query)
    {

        // Fungsi ini hanya sebagai contoh, logging utama ada di catch block
        // Anda bisa mengaktifkan ini jika ingin me-log SEMUA query, bukan hanya yang error
        // log_query($query);
    }

    public function __destruct()
    {

        $this->db->close();
    }

}