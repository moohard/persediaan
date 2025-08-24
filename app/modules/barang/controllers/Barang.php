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

    // Menampilkan halaman utama dengan tabel dan modal
    public function index()
    {

        $data['title']     = 'Manajemen Data Barang';
        $data['js_module'] = 'barang';
        $this->view('barang', 'index_view', $data);
    }

    // Menampilkan halaman sampah (trash)
    public function trash()
    {

        $data['title']  = 'Data Barang Dihapus (Sampah)';
        $data['barang'] = $this->barangModel->getAllTrashed();
        $this->view('barang', 'trash_view', $data);
    }

    // Memulihkan barang dari sampah
    public function restore()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/barang/trash');
        verify_csrf_token();

        $id = $this->encryption->decrypt($_POST['id']);
        if (!$id) die('ID tidak valid.');

        if ($this->barangModel->restore($id))
        {
            set_flash_message('success', 'Data barang berhasil dipulihkan.');
        } else
        {
            set_flash_message('danger', 'Gagal memulihkan data barang.');
        }
        $this->redirect('/barang/trash');
    }

    // Method API untuk menangani semua request AJAX
    public function api($method = '', $param = '')
    {

        header('Content-Type: application/json');

        // PERBAIKAN: Menggunakan $_SERVER untuk cara yang lebih andal dalam membaca header
        $is_ajax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$is_ajax)
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Akses tidak sah: Permintaan harus melalui AJAX.' ]);

            return;
        }

        if (empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Akses tidak sah: Token CSRF tidak valid.' ]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), TRUE);

        switch ($method)
        {
            case 'getAll':
                $barang = $this->barangModel->getAllActive();
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
            case 'update':
                $errors = $this->validate_input($input);
                if (!empty($errors))
                {
                    http_response_code(422);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Validasi gagal!', 'errors' => $errors ]);
                    return;
                }

                if ($method === 'create')
                {
                    $result  = $this->barangModel->create($input);
                    $message = 'Data berhasil ditambahkan.';
                } else
                {
                    $input['id'] = $this->encryption->decrypt($input['id']);
                    if (!$input['id'])
                    {
                        echo json_encode([ 'success' => FALSE, 'message' => 'ID tidak valid.' ]);
                        return;
                    }
                    $result  = $this->barangModel->update($input['id'], $input);
                    $message = 'Data berhasil diperbarui.';
                }

                if ($result['success'])
                {
                    echo json_encode([ 'success' => TRUE, 'message' => $message ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => $result['message'] ]);
                }
                break;

            case 'delete':
                $id = $this->encryption->decrypt($input['id']);
                if (!$id)
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'ID tidak valid.' ]);
                    return;
                }
                $result = $this->barangModel->softDelete($id);
                if ($result['success'])
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil dipindahkan ke sampah.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => $result['message'] ]);
                }
                break;
        }
    }

    private function validate_input($input)
    {

        $errors = [];
        if (empty($input['kode_barang'])) $errors[] = 'Kode barang tidak boleh kosong.';
        if (strlen($input['kode_barang']) > 50) $errors[] = 'Kode barang maksimal 50 karakter.';
        if (empty($input['nama_barang'])) $errors[] = 'Nama barang tidak boleh kosong.';
        if (!isset($input['stok']) || !is_numeric($input['stok']) || $input['stok'] < 0) $errors[] = 'Stok harus berupa angka positif.';
        if (!in_array($input['jenis_barang'], [ 'habis_pakai', 'aset' ])) $errors[] = 'Jenis barang tidak valid.';
        return $errors;
    }

}

?>