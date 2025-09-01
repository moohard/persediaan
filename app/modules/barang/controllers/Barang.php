<?php

require_once APP_PATH . '/core/Controller.php';

class Barang extends Controller
{

    private $barangModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->barangModel = $this->model('barang', 'Barang_model');
    }

    public function index()
    {

        if (!has_permission('barang_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses.' ]);
        }
        $data['title']     = 'Manajemen Barang';
        $data['js_module'] = 'barang';
        $this->view('barang', 'index_view', $data);
    }

    public function trash()
    {

        if (!has_permission('barang_trash'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses.' ]);
        }
        $data['title']     = 'Data Barang Dihapus';
        $data['js_module'] = 'barang';
        $this->view('barang', 'trash_view', $data);
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
            case 'getById':
            case 'getKategori':
            case 'getSatuan':
                if (!has_permission('barang_view'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }

                if ($method === 'getAll')
                {
                    $barang = $this->barangModel->getAllActive();
                    foreach ($barang as &$item)
                    {
                        $item['id_barang_encrypted'] = $this->encryption->encrypt($item['id_barang']);
                    }
                    echo json_encode([ 'success' => TRUE, 'data' => $barang ]);
                } elseif ($method === 'getById')
                {
                    $id     = $this->encryption->decrypt($param);
                    $barang = $this->barangModel->getById($id);
                    if ($barang)
                    {
                        echo json_encode([ 'success' => TRUE, 'data' => $barang ]);
                    } else
                    {
                        http_response_code(404);
                        echo json_encode([ 'success' => FALSE, 'message' => 'Barang tidak ditemukan.' ]);
                    }
                } elseif ($method === 'getKategori')
                {
                    $kategori = $this->barangModel->getAllKategori();
                    echo json_encode([ 'success' => TRUE, 'data' => $kategori ]);
                } elseif ($method === 'getSatuan')
                {
                    $satuan = $this->barangModel->getAllSatuan();
                    echo json_encode([ 'success' => TRUE, 'data' => $satuan ]);
                }
                break;

            case 'create':
                if (!has_permission('barang_create'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }

                $data = $input;
                $result = $this->barangModel->create($data, $_SESSION['user_id']);
                echo json_encode($result);
                break;

            case 'update':
                if (!has_permission('barang_update'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }

                $id = $this->encryption->decrypt($input['id_barang_encrypted'] ?? NULL);
                if (!$id)
                {
                    http_response_code(400);
                    echo json_encode([ 'success' => FALSE, 'message' => 'ID Barang tidak valid.' ]);
                    return;
                }
                $data = $input;
                $result = $this->barangModel->update($id, $data, $_SESSION['user_id']);
                echo json_encode($result);
                break;

            case 'delete':
                if (!has_permission('barang_delete'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }

                $id = $this->encryption->decrypt($input['id'] ?? NULL);
                if (!$id)
                {
                    http_response_code(400);
                    echo json_encode([ 'success' => FALSE, 'message' => 'ID Barang tidak valid.' ]);
                    return;
                }
                $result = $this->barangModel->softDelete($id, $_SESSION['user_id']);
                echo json_encode($result);
                break;

            case 'trash':
            case 'restore':
                if (!has_permission('barang_trash'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }

                if ($method === 'getTrash')
                {
                    $barang = $this->barangModel->getTrash();
                    foreach ($barang as &$item)
                    {
                        $item['id_barang_encrypted'] = $this->encryption->encrypt($item['id_barang']);
                    }
                    echo json_encode([ 'success' => TRUE, 'data' => $barang ]);
                } elseif ($method === 'restore')
                {
                    $id = $this->encryption->decrypt($input['id'] ?? NULL);
                    if (!$id)
                    {
                        http_response_code(400);
                        echo json_encode([ 'success' => FALSE, 'message' => 'ID Barang tidak valid.' ]);
                        return;
                    }
                    $result = $this->barangModel->restore($id, $_SESSION['user_id']);
                    echo json_encode($result);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode([ 'success' => FALSE, 'message' => 'Endpoint tidak ditemukan.' ]);
                break;
        }
    }

}