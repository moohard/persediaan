<?php
require_once APP_PATH . '/core/Controller.php';

class Pembelian extends Controller {
    private $pembelianModel;

    public function __construct() {
        parent::__construct();
        // Hanya admin yang bisa mengakses modul ini
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect('/dashboard');
        }
        $this->pembelianModel = $this->model('pembelian', 'Pembelian_model');
    }

    public function index() {
        $data['title'] = 'Proses Permintaan Pembelian';
        $data['js_module'] = 'pembelian';
        $this->view('pembelian', 'index_view', $data);
    }

    public function api($method = '') {
        header('Content-Type: application/json');
        
        $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$is_ajax || empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Akses tidak sah.']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);

        switch ($method) {
            case 'getApproved':
                $permintaan = $this->pembelianModel->getApprovedPurchaseRequests();
                foreach ($permintaan as &$item) {
                    $item['id_permintaan_encrypted'] = $this->encryption->encrypt($item['id_permintaan']);
                }
                echo json_encode(['success' => true, 'data' => $permintaan]);
                break;
            
            case 'validatePurchase':
                $id = $this->encryption->decrypt($input['id']);
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID Permintaan tidak valid.']);
                    return;
                }
                $result = $this->pembelianModel->validatePurchase($id, $_SESSION['user_id']);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => 'Pembelian berhasil divalidasi.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
                break;
        }
    }
}