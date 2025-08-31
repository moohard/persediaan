<?php

require_once APP_PATH . '/core/Controller.php';

class Auth extends Controller
{

    private $authModel;

    public function __construct()
    {

        parent::__construct();
        if (isset($_SESSION['user_id']) && strpos($_SERVER['REQUEST_URI'], '/auth/logout') === FALSE)
        {
            $this->redirect('/dashboard');
        }
        $this->authModel = $this->model('auth', 'Auth_model');
    }

    public function index()
    {

        // [PERBAIKAN] Tidak perlu lagi memanggil set_csrf_token() di sini.
        $data['title']     = 'Login';
        $data['js_module'] = 'auth';
        $this->view('auth', 'login_view', $data);
    }

    public function logout()
    {

        session_destroy();
        $this->redirect('/auth');
    }

    public function api($method = '')
    {

        header('Content-Type: application/json');

        $is_ajax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$is_ajax || empty($csrf_token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Akses tidak sah.' ]);

            return;
        }

        if ($method === 'process_login')
        {
            $input    = json_decode(file_get_contents('php://input'), TRUE);
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';

            // Validasi server-side
            if (empty($username) || empty($password))
            {
                http_response_code(400);
                echo json_encode([ 'success' => FALSE, 'message' => 'Username dan password wajib diisi.' ]);
                return;
            }

            try
            {
                $result = $this->authModel->processLogin($username, $password);
            } catch (PDOException $e)
            {
                echo "Database Error: " . $e->getMessage();
            }
            echo json_encode($result);
        }
    }

}