<?php

class Model
{

    protected $db;

    public function __construct()
    {

        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error)
        {
            if (ENVIRONMENT === 'development')
            {
                die("Koneksi database gagal: " . $this->db->connect_error);
            } else
            {
                die("Tidak dapat terhubung ke server database.");
            }
        }
    }

    public function __destruct()
    {

        $this->db->close();
    }

}
