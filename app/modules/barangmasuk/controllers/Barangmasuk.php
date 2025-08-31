<?php

require_once APP_PATH . '/core/Controller.php';

class Barangmasuk extends Controller
{

    private $barangMasukModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->barangMasukModel = $this->model('barangmasuk', 'Barangmasuk_model');
    }

    public function index()
    {

        if (!has_permission('barangmasuk_process'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses.' ]);
        }
        $data['title']     = 'Penerimaan Barang';
        $data['js_module'] = 'barangmasuk';
        $this->view('barangmasuk', 'index_view', $data);
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

        $input = json_decode(file_get_contents('php://input'), TRUE);
        switch ($method)
        {
            case 'getPurchasedRequests':
                $requests = $this->barangMasukModel->getProcessedPurchaseRequests();
                foreach ($requests as &$req)
                {
                    $req['id_permintaan_encrypted'] = $this->encryption->encrypt($req['id_permintaan']);
                }
                echo json_encode([ 'success' => TRUE, 'data' => $requests ]);
                break;

            case 'getDetail':
                $id = $this->encryption->decrypt($param);
                if (!$id)
                {
                    http_response_code(400);
                    echo json_encode([ 'success' => FALSE, 'message' => 'ID tidak valid.' ]);
                    return;
                }
                $details = $this->barangMasukModel->getRequestDetailsForReceipt($id);

                if ($details)
                {
                    echo json_encode([ 'success' => TRUE, 'data' => $details ]);
                } else
                {
                    http_response_code(404);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Data permintaan tidak ditemukan.' ]);
                }
                break;

            case 'processReceipt':
                $id = $this->encryption->decrypt($input['id_permintaan_encrypted'] ?? NULL);
                $itemsAlokasi = $input['items'] ?? [];

                if (!$id || empty($itemsAlokasi))
                {
                    http_response_code(400);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Data tidak lengkap.' ]);
                    return;
                }

                $result = $this->barangMasukModel->processReceipt($id, $_SESSION['user_id'], $itemsAlokasi);
                echo json_encode($result);
                break;
        }
    }

}