<?php

require_once APP_PATH . '/core/Model.php';

class Auth_model extends Model
{

    /**
     * Mengambil data pengguna dari database berdasarkan username.
     * @param string $username
     * @return array|null Data pengguna atau null jika tidak ditemukan.
     */
    public function getUserByUsername($username)
    {

        // PERBAIKAN: Mengambil data langsung dari database menggunakan prepared statement
        $query = "SELECT 
                    id_pengguna AS id, 
                    nama_lengkap AS nama, 
                    password, 
                    role 
                  FROM tbl_pengguna 
                  WHERE username = ? AND is_active = 1";

        try
        {
            $stmt = $this->db->prepare($query);
            if ($stmt === FALSE)
            {
                throw new Exception("Prepare statement failed: (" . $this->db->errno . ") " . $this->db->error);
            }

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_assoc(); // Mengembalikan data user atau null jika tidak ditemukan

        } catch (Exception $e)
        {
            // Log error di mode development
            if (ENVIRONMENT === 'development')
            {
                log_query($query, $e->getMessage());
            }
            return NULL; // Gagal mengambil data
        }
    }

}