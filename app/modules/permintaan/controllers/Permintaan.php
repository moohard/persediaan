<?php

require_once APP_PATH . '/core/Controller.php';

class Permintaan extends Controller
{

    private $permintaanModel;

    private $barangModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->permintaanModel = $this->model('permintaan', 'Permintaan_model');
        $this->barangModel     = $this->model('barang', 'Barang_model');
    }

    public function index()
    {

        if (!has_permission('permintaan_view_own') && !has_permission('permintaan_view_all'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses.' ]);
        }
        $data['title']     = 'Permintaan Barang';
        $data['js_module'] = 'permintaan';
        $this->view('permintaan', 'index_view', $data);
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
            case 'getAll':
            case 'getDetail':
                if (!has_permission('permintaan_view_own') && !has_permission('permintaan_view_all'))
                {
                    http_response_code(403);

                    /* ... tolak akses ... */
                    return;
                }
                if ($method === 'getAll')
                {

                    $requests = $this->permintaanModel->getAllRequests($_SESSION['user_id'], $_SESSION['nama_role']);
                    foreach ($requests as &$req)
                    {
                        $req['id_permintaan_encrypted'] = encrypt_id($req['id_permintaan']);
                    }

                    echo json_encode([ 'success' => TRUE, 'data' => $requests ]);
                } elseif ($method === 'getDetail')
                {
                    // AMBIL ID DARI PARAMETER PERTAMA
                    $encryptedId = $param ?? '';

                    if (empty($encryptedId))
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'ID tidak provided.' ]);
                        return;
                    }

                    // Decrypt ID
                    $id = decrypt_id($encryptedId);

                    if (!$id || !is_numeric($id))
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'ID tidak valid.' ]);
                        return;
                    }

                    $details = $this->permintaanModel->getRequestDetailsById($id);
                    if ($details)
                    {
                        echo json_encode([ 'success' => TRUE, 'data' => $details ]);
                    } else
                    {
                        http_response_code(404);
                        echo json_encode([ 'success' => FALSE, 'message' => 'Data permintaan tidak ditemukan.' ]);
                    }
                }
                break;
            case 'getAvailableItems': // Boleh diakses jika bisa membuat permintaan
            case 'create':
                if (!has_permission('permintaan_create'))
                {
                    http_response_code(403); /* ... tolak akses ... */
                    return;
                }
                if ($method === 'getAvailableItems')
                {
                    $items = $this->barangModel->getAllActive();
                    echo json_encode([ 'success' => TRUE, 'data' => $items ]);
                } elseif ($method === 'create')
                {
                    $data            = $input['data'] ?? [];
                    $tipe_permintaan = $input['tipe_permintaan'] ?? 'stok';

                    if (empty($data['items']) || !is_array($data['items']))
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'Minimal harus ada 1 barang yang diminta.' ]);
                        return;
                    }

                    $result = $this->permintaanModel->createRequest($data, $_SESSION['user_id'], $tipe_permintaan);
                    if ($result['success'])
                    {
                        echo json_encode([ 'success' => TRUE, 'message' => 'Permintaan berhasil dibuat.' ]);
                    } else
                    {
                        http_response_code(500);
                        echo json_encode([ 'success' => FALSE, 'message' => $result['message'] ]);
                    }
                }
                break;

            case 'approve':
            case 'reject':
                if (!has_permission('permintaan_approve'))
                {
                    http_response_code(403); /* ... tolak akses ... */
                    return;
                }
                if ($method === 'approve')
                {

                    $id = decrypt_id($input['id'] ?? NULL);

                    $catatan = $input['catatan'] ?? '';
                    $items   = $input['items'] ?? [];

                    if (!$id)
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'ID Permintaan tidak valid.' ]);
                        return;
                    }

                    $result = $this->permintaanModel->approveRequest($id, $_SESSION['user_id'], $catatan, $items);
                    echo json_encode($result);
                } elseif ($method === 'reject')
                {
                    $id      = decrypt_id($input['id'] ?? NULL);
                    $catatan = $input['catatan'] ?? '';

                    if (!$id)
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'ID Permintaan tidak valid.' ]);
                        return;
                    }
                    if (empty($catatan))
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'Catatan penolakan wajib diisi.' ]);
                        return;
                    }

                    $result = $this->permintaanModel->rejectRequest($id, $_SESSION['user_id'], $catatan);
                    echo json_encode($result);
                }
                break;
        }
    }

}