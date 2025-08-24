<?php

require_once APP_PATH . '/core/Controller.php';

class Auth extends Controller
{

    public function __construct()
    {

        parent::__construct();
        // Cek jika user sudah login, JANGAN biarkan akses halaman login/register, KECUALI untuk logout
        if (isset($_SESSION['user_id']) && strpos($_SERVER['REQUEST_URI'], 'logout') === FALSE)
        {
            $this->redirect('/dashboard');
        }
    }

    public function index()
    {

        generate_csrf_token();
        $data['title'] = 'Login';
        $data['error'] = $_SESSION['error_message'] ?? NULL;
        unset($_SESSION['error_message']);

        if (isset($_SESSION['lockout_time']) && time() - $_SESSION['lockout_time'] < LOCKOUT_TIME)
        {
            $remaining_time    = LOCKOUT_TIME - (time() - $_SESSION['lockout_time']);
            $data['error']     = "Terlalu banyak percobaan. Coba lagi dalam " . ceil($remaining_time / 60) . " menit.";
            $data['is_locked'] = TRUE;
        } else
        {
            unset($_SESSION['lockout_time'], $_SESSION['login_attempts']);
            $data['is_locked'] = FALSE;
        }

        $this->view('auth', 'login_view', $data);
    }

    public function process_login()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/auth');

        verify_csrf_token();

        $username = $_POST['username'];
        $password = $_POST['password'];

        $authModel = $this->model('auth', 'Auth_model');
        $user      = $authModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password']))
        {
            // PERBAIKAN: Set session DULU, baru regenerate ID
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama'];
            $_SESSION['role']         = $user['role'];
            session_regenerate_id(TRUE); // Mencegah session fixation

            unset($_SESSION['login_attempts'], $_SESSION['lockout_time']);

            $this->redirect('/dashboard');
        } else
        {
            if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempts']++;

            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS)
            {
                $_SESSION['lockout_time']  = time();
                $_SESSION['error_message'] = "Akun Anda diblokir sementara.";
            } else
            {
                $remaining                 = MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts'];
                $_SESSION['error_message'] = "Username atau password salah. Sisa percobaan: $remaining.";
            }
            $this->redirect('/auth');
        }
    }

    public function logout()
    {

        session_unset();
        session_destroy();
        $this->redirect('/auth');
    }

}

?>