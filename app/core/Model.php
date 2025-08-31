<?php

class Model
{

    protected $db;

    public function __construct()
    {

        // Membuat koneksi mysqli baru
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Memeriksa error koneksi
        if ($this->db->connect_error)
        {
            // Di development, tampilkan error. Di production, tampilkan pesan generik.
            if (ENVIRONMENT === 'development')
            {
                die("Koneksi gagal: " . $this->db->connect_error);
            } else
            {
                die("Terjadi masalah koneksi database. Silakan coba lagi nanti.");
            }
        }

        // Mengatur set karakter ke utf8mb4
        $this->db->set_charset("utf8mb4");
    }

    // Destructor untuk menutup koneksi saat objek dihancurkan
    public function __destruct()
    {

        if ($this->db)
        {
            $this->db->close();
        }
    }

}