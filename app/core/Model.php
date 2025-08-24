<?php

class Model
{

    protected $db;

    public function __construct()
    {

        // PERBAIKAN: Aktifkan mode exception untuk mysqli agar error database bisa ditangkap
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try
        {
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $this->db->set_charset('utf8mb4'); // Set charset untuk praktik terbaik
        } catch (mysqli_sql_exception $e)
        {
            // Jangan tampilkan detail error di mode production
            if (ENVIRONMENT === 'development')
            {
                die("Koneksi database gagal: " . $e->getMessage());
            } else
            {
                // Catat error ke log server di mode production
                error_log("Database connection error: " . $e->getMessage());
                die("Tidak dapat terhubung ke server database.");
            }
        }
    }

    public function __destruct()
    {

        $this->db->close();
    }

}

?>