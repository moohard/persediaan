<?php

require_once APP_PATH . '/core/Controller.php';

class Stockopname extends Controller
{

    private $stockOpnameModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !has_permission('stock_opname_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses ke halaman ini.' ]);
        }
        $this->stockOpnameModel = $this->model('stockopname', 'Stockopname_model');
    }

    public function index()
    {

        $data['title']     = 'Stock Opname';
        $data['js_module'] = 'stockopname';
        $this->view('stockopname', 'index_view', $data);
    }

    public function api($method = '')
    {

        header('Content-Type: application/json');
        // (Pengecekan AJAX & CSRF seperti biasa)
        $is_ajax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$is_ajax || empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Akses tidak sah.' ]);

            return;
        }

        $input = json_decode(file_get_contents('php://input'), TRUE);

        switch ($method)
        {
            case 'getHistory':
                $history = $this->stockOpnameModel->getHistory();
                echo json_encode([ 'success' => TRUE, 'data' => $history ]);
                break;

            case 'getLatestStock':
                $stock = $this->stockOpnameModel->getLatestStockData();
                echo json_encode([ 'success' => TRUE, 'data' => $stock ]);
                break;

            case 'save':
                if (!has_permission('stock_opname_create'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }
                $keterangan = $input['keterangan'] ?? '';
                $items = $input['items'] ?? [];
                $result = $this->stockOpnameModel->saveOpname($keterangan, $items, $_SESSION['user_id']);
                echo json_encode($result);
                break;
        }
    }

}