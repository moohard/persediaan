<?php

require_once APP_PATH . '/core/Model.php';

class Auth_model extends Model
{

    /**
     * [METHOD BARU] Memproses seluruh logika login.
     */
    public function processLogin($username, $password)
    {

        $user = $this->getUserByUsername($username);

        // Bypass untuk developer (tanpa cek password hash)
        if ($username === 'developer' && $user && $password === 'devpass')
        {
            $this->clearLoginAttempts($username);
            $this->createUserSession($user);

            return [ 'success' => TRUE, 'message' => 'Selamat datang, Developer!', 'redirect_url' => BASE_URL . '/dashboard' ];
        }

        $attempts = $_SESSION['login_attempts'][$username] ?? NULL;
        if ($attempts && $attempts['attempts'] >= MAX_LOGIN_ATTEMPTS && (time() - $attempts['last_attempt']) < LOCKOUT_TIME)
        {
            return [ 'success' => FALSE, 'message' => 'Terlalu banyak percobaan. Akun terkunci selama 5 menit.' ];
        }

        if ($user && password_verify($password, $user['password']))
        {
            $this->clearLoginAttempts($username);
            $this->createUserSession($user);
            return [ 'success' => TRUE, 'message' => 'Login berhasil!', 'redirect_url' => BASE_URL . '/dashboard' ];
        } else
        {
            $this->recordLoginAttempt($username);
            return [ 'success' => FALSE, 'message' => 'Username atau password salah.' ];
        }
    }

    private function createUserSession($user)
    {

        session_regenerate_id(TRUE);
        $_SESSION['user_id']   = $user['id_pengguna'];
        $_SESSION['nama']      = $user['nama_lengkap'];
        $_SESSION['id_role']   = $user['id_role'];
        $_SESSION['nama_role'] = $user['nama_role']; // Simpan nama role

        // Ambil dan simpan semua izin pengguna ke sesi
        $_SESSION['permissions'] = $this->getUserPermissions($user['id_role']);

        $_SESSION['last_regen'] = time();
    }

    public function getUserByUsername($username)
    {

        $stmt = $this->db->prepare("
            SELECT u.*, r.nama_role 
            FROM tbl_pengguna u 
            LEFT JOIN tbl_roles r ON u.id_role = r.id_role
            WHERE u.username = ? AND u.is_active = 1
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserPermissions($id_role)
    {

        $permissions = [];
        if (empty($id_role)) return $permissions;

        $stmt = $this->db->prepare("
            SELECT p.nama_permission 
            FROM tbl_role_permissions rp
            JOIN tbl_permissions p ON rp.id_permission = p.id_permission
            WHERE rp.id_role = ?
        ");
        $stmt->bind_param("i", $id_role);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc())
        {
            $permissions[] = $row['nama_permission'];
        }
        return $permissions;
    }

    public function getLoginAttempts($username)
    {

        // Di aplikasi nyata, ini akan mengambil dari tabel 'login_attempts'
        // Untuk saat ini, kita simulasikan dengan session
        return $_SESSION['login_attempts'][$username] ?? NULL;
    }

    public function recordLoginAttempt($username)
    {

        if (!isset($_SESSION['login_attempts'][$username]))
        {
            $_SESSION['login_attempts'][$username] = [ 'attempts' => 0, 'last_attempt' => 0 ];
        }
        $_SESSION['login_attempts'][$username]['attempts']++;
        $_SESSION['login_attempts'][$username]['last_attempt'] = time();
    }

    public function clearLoginAttempts($username)
    {

        if (isset($_SESSION['login_attempts'][$username]))
        {
            unset($_SESSION['login_attempts'][$username]);
        }
    }

}