<?php

require_once APP_PATH . '/core/Controller.php';

class Auth extends Controller
{

    public function __construct()
    {

        parent::__construct();
        if (isset($_SESSION['user_id']) && strpos($_SERVER['REQUEST_URI'], 'logout') === false) {
            $this->redirect('/dashboard');
        }
    }

    public function index()
    {

        generate_csrf_token();
        $data['title']     = 'Login';
        $data['js_module'] = 'auth';
        $this->view('auth', 'login_view', $data);
    }

    public function api($method = '')
    {

        header('Content-Type: application/json');

        // PERBAIKAN: Menggunakan $_SERVER untuk cara yang lebih andal dalam membaca header
        $is_ajax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$is_ajax)
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Akses tidak sah: Permintaan harus melalui AJAX.' ]);

            return;
        }

        if (empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Akses tidak sah: Token CSRF tidak valid.' ]);
            return;
        }

        if ($method === 'process_login')
        {
            $this->process_login();
        } else
        {
            http_response_code(404);
            echo json_encode([ 'success' => FALSE, 'message' => 'Endpoint tidak ditemukan.' ]);
        }
    }

    private function process_login()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            http_response_code(405);
            echo json_encode([ 'success' => FALSE, 'message' => 'Metode tidak diizinkan.' ]);
            return;
        }

        if (isset($_SESSION['lockout_time']) && time() - $_SESSION['lockout_time'] < LOCKOUT_TIME)
        {
            $remaining_time = LOCKOUT_TIME - (time() - $_SESSION['lockout_time']);
            $message        = "Terlalu banyak percobaan. Coba lagi dalam " . ceil($remaining_time / 60) . " menit.";
            http_response_code(429);
            echo json_encode([ 'success' => FALSE, 'message' => $message ]);
            return;
        }
        unset($_SESSION['lockout_time']);

        $input    = json_decode(file_get_contents('php://input'), TRUE);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        $errors = [];
        if (empty($username)) $errors[] = 'Username tidak boleh kosong.';
        if (empty($password)) $errors[] = 'Password tidak boleh kosong.';
        if (strlen($username) > 50) $errors[] = 'Username terlalu panjang (maksimal 50 karakter).';

        if (!empty($errors))
        {
            http_response_code(422);
            echo json_encode([ 'success' => FALSE, 'message' => 'Validasi gagal!', 'errors' => $errors ]);
            return;
        }

        $authModel = $this->model('auth', 'Auth_model');
        $user      = $authModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password']))
        {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama'];
            $_SESSION['role']         = $user['role'];
            session_regenerate_id(TRUE);

            unset($_SESSION['login_attempts']);

            echo json_encode([ 'success' => TRUE, 'message' => 'Login berhasil!', 'redirect_url' => BASE_URL . '/dashboard' ]);
        } else
        {
            if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempts']++;

            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS)
            {
                $_SESSION['lockout_time'] = time();
                $message                  = "Akun Anda diblokir sementara karena terlalu banyak percobaan.";
            } else
            {
                $remaining = MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts'];
                $message   = "Username atau password salah. Sisa percobaan: $remaining.";
            }
            http_response_code(401);
            echo json_encode([ 'success' => FALSE, 'message' => $message ]);
        }
    }

    public function logout()
    {

        session_unset();
        session_destroy();
        $this->redirect('/auth');
    }

}