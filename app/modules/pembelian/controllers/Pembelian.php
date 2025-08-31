<?php

require_once APP_PATH . '/core/Controller.php';

class Pembelian extends Controller
{

    private $pembelianModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->pembelianModel = $this->model('pembelian', 'Pembelian_model');
    }

    public function index()
    {

        if (!has_permission('pembelian_process'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses.' ]);
        }
        $data['title']     = 'Proses Pembelian';
        $data['js_module'] = 'pembelian';
        $this->view('pembelian', 'index_view', $data);
    }

    public function api($method = '', $param = '')
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
        if (!has_permission('pembelian_process'))
        {
            http_response_code(403); /* ... tolak akses ... */
            return;
        }
        switch ($method)
        {
            case 'getPurchaseRequests':
                $requests = $this->pembelianModel->getPurchaseRequestsToProcess();
                foreach ($requests as &$req)
                {
                    $req['id_permintaan_encrypted'] = $this->encryption->encrypt($req['id_permintaan']);
                }
                echo json_encode([ 'success' => TRUE, 'data' => $requests ]);
                break;

            // [FITUR BARU] Endpoint untuk menandai sudah dibeli
            case 'markAsPurchased':
                $input = json_decode(file_get_contents('php://input'), TRUE);
                $id = $this->encryption->decrypt($input['id'] ?? NULL);
                if (!$id)
                {
                    http_response_code(400);
                    echo json_encode([ 'success' => FALSE, 'message' => 'ID Permintaan tidak valid.' ]);
                    return;
                }
                $result = $this->pembelianModel->markAsPurchased($id, $_SESSION['user_id']);
                echo json_encode($result);
                break;
        }
    }

}