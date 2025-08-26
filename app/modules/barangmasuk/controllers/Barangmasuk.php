<?php

require_once APP_PATH . '/core/Controller.php';

class Barangmasuk extends Controller
{

    private $barangMasukModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [ 'admin', 'developer' ]))
        {
            $this->redirect('/dashboard');
        }
        $this->barangMasukModel = $this->model('barangmasuk', 'Barangmasuk_model');
    }

    public function index()
    {

        $data['title']     = 'Penerimaan Barang Masuk';
        $data['js_module'] = 'barangmasuk';
        $this->view('barangmasuk', 'index_view', $data);
    }

    public function api($method = '')
    {

        header('Content-Type: application/json');

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
            case 'getPurchased':
                $permintaan = $this->barangMasukModel->getPurchasedRequests();
                foreach ($permintaan as &$item)
                {
                    $item['id_permintaan_encrypted'] = $this->encryption->encrypt($item['id_permintaan']);
                }
                echo json_encode([ 'success' => TRUE, 'data' => $permintaan ]);
                break;

            case 'processReceipt':
                $id = $this->encryption->decrypt($input['id']);
                if (!$id)
                {
                    http_response_code(400);
                    echo json_encode([ 'success' => FALSE, 'message' => 'ID Permintaan tidak valid.' ]);
                    return;
                }
                $result = $this->barangMasukModel->processGoodsReceipt($id, $_SESSION['user_id']);
                if ($result['success'])
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Barang berhasil diterima dan stok diperbarui.' ]);
                } else
                {
                    http_response_code(500);
                    echo json_encode([ 'success' => FALSE, 'message' => $result['message'] ]);
                }
                break;
        }
    }

}