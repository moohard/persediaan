<?php
require_once APP_PATH . '/core/Controller.php';

class Pengaturan extends Controller
{
    private $pengaturanModel;

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('pengaturan_view')) {
            $this->redirect('/dashboard', ['type' => 'danger', 'message' => 'Anda tidak memiliki akses.']);
        }
        $this->pengaturanModel = $this->model('pengaturan', 'Pengaturan_model');
    }

    public function index()
    {
        $data['title']     = 'Pengaturan Sistem';
        $data['js_module'] = 'pengaturan';
        $this->view('pengaturan', 'index_view', $data);
    }

    public function api($method = '')
    {
        header('Content-Type: application/json');

        $is_ajax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$is_ajax || empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Akses tidak sah.']);
            return;
        }

        switch ($method) {
            case 'getAll':
                if (!has_permission('pengaturan_view')) { http_response_code(403); return; }
                $settings = $this->pengaturanModel->getAllSettings();
                echo json_encode(['success' => true, 'data' => $settings]);
                break;
            
            case 'save':
                if (!has_permission('pengaturan_update')) {
                     http_response_code(403); echo json_encode(['success' => false, 'message' => 'Akses ditolak.']); return;
                }
                $input = json_decode(file_get_contents('php://input'), true);
                $result = $this->pengaturanModel->saveSettings($input);
                echo json_encode($result);
                break;

            case 'clearTransactions':
                if (!has_permission('pengaturan_clear_transactions')) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
                    return;
                }
                $result = $this->pengaturanModel->clearAllTransactions();
                echo json_encode($result);
                break;
            
            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Endpoint tidak ditemukan.']);
                break;
        }
    }
}