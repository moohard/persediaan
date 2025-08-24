<?php

require_once APP_PATH . '/core/Controller.php';

class Barang extends Controller
{

    private $barangModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
        {
            set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect('/dashboard');
        }
        $this->barangModel = $this->model('barang', 'Barang_model');
    }

    public function index()
    {

        $data['title']     = 'Manajemen Data Barang';
        $data['js_module'] = 'barang';
        $this->view('barang', 'index_view', $data);
    }

    // Metode API untuk AJAX
    public function api($method = '', $param = '')
    {

        header('Content-Type: application/json');

        $headers = getallheaders();
        if (!isset($headers['X-Csrf-Token']) || !hash_equals($_SESSION['csrf_token'], $headers['X-Csrf-Token']))
        {
            echo json_encode([ 'success' => FALSE, 'message' => 'Invalid CSRF Token' ]);

            return;
        }

        switch ($method)
        {
            case 'getAll':
                $barang = $this->barangModel->getAll();
                foreach ($barang as &$item)
                {
                    $item['id_barang_encrypted'] = $this->encryption->encrypt($item['id_barang']);
                }
                echo json_encode([ 'success' => TRUE, 'data' => $barang ]);
                break;

            case 'getById':
                $id = $this->encryption->decrypt($param);
                $item = $this->barangModel->getById($id);
                echo json_encode([ 'success' => TRUE, 'data' => $item ]);
                break;

            case 'create':
                $data = [
                    'kode_barang'  => $_POST['kode_barang'],
                    'nama_barang'  => $_POST['nama_barang'],
                    'jenis_barang' => $_POST['jenis_barang'],
                    'stok'         => (int) $_POST['stok']
                ];
                if ($this->barangModel->create($data))
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil ditambahkan.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'Gagal menambahkan data.' ]);
                }
                break;

            case 'update':
                $data = [
                    'id'           => $this->encryption->decrypt($_POST['id']),
                    'kode_barang'  => $_POST['kode_barang'],
                    'nama_barang'  => $_POST['nama_barang'],
                    'jenis_barang' => $_POST['jenis_barang'],
                    'stok'         => (int) $_POST['stok']
                ];
                if ($this->barangModel->update($data['id'], $data))
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil diperbarui.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'Gagal memperbarui data.' ]);
                }
                break;

            case 'delete':
                $id = $this->encryption->decrypt($_POST['id']);
                if ($this->barangModel->delete($id))
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil dihapus.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'Gagal menghapus data.' ]);
                }
                break;
        }
    }

}
