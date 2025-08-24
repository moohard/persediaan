<?php

require_once APP_PATH . '/core/Controller.php';

class Permintaan extends Controller
{

    private $permintaanModel;

    public function __construct()
    {

        parent::__construct();
        // Semua method di controller ini memerlukan login
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->permintaanModel = $this->model('permintaan', 'Permintaan_model');
    }

    // Menampilkan halaman utama daftar permintaan
    public function index()
    {

        $data['title']      = 'Daftar Permintaan Barang';
        $data['js_module']  = 'permintaan';
        $data['permintaan'] = $this->permintaanModel->getAllPermintaan();

        // PERBAIKAN: Gunakan helper model() untuk memuat model dari modul lain
        $barangModel         = $this->model('barang', 'Barang_model');
        $data['barang_list'] = $barangModel->getAll();

        $this->view('permintaan', 'index_view', $data);
    }

    // Method API untuk menangani request AJAX
    public function api($method = '')
    {

        header('Content-Type: application/json');

        // Validasi CSRF untuk AJAX
        $headers    = getallheaders();
        $csrf_token = $headers['X-Csrf-Token'] ?? $_POST['csrf_token'] ?? '';
        if (empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
        {
            http_response_code(403);
            echo json_encode([ 'success' => FALSE, 'message' => 'Invalid CSRF Token' ]);

            return;
        }

        switch ($method)
        {
            case 'store':
                $this->store();
                break;
            default:
                http_response_code(404);
                echo json_encode([ 'success' => FALSE, 'message' => 'API endpoint not found.' ]);
                break;
        }
    }

    // Logika untuk menyimpan permintaan baru (dipanggil oleh API)
    private function store()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            http_response_code(405);
            echo json_encode([ 'success' => FALSE, 'message' => 'Method not allowed.' ]);
            return;
        }

        // Ambil data JSON dari body request
        $json_data = file_get_contents('php://input');
        $post_data = json_decode($json_data, TRUE);

        // --- Validasi Input Sisi Server ---
        $catatan = $post_data['catatan_pemohon'] ?? '';
        $items   = $post_data['items'] ?? [];
        $errors  = [];

        if (empty($catatan))
        {
            $errors[] = 'Catatan atau keperluan harus diisi.';
        }
        if (empty($items) || !is_array($items))
        {
            $errors[] = 'Minimal harus ada satu barang yang diminta.';
        } else
        {
            foreach ($items as $index => $item)
            {
                if (empty($item['id_barang']) || !is_numeric($item['id_barang']))
                {
                    $errors[] = 'Barang pada baris #' . ($index + 1) . ' tidak valid.';
                }
                if (empty($item['jumlah']) || !is_numeric($item['jumlah']) || $item['jumlah'] < 1)
                {
                    $errors[] = 'Jumlah untuk barang pada baris #' . ($index + 1) . ' harus diisi dan minimal 1.';
                }
            }
        }

        if (!empty($errors))
        {
            http_response_code(400); // Bad Request
            echo json_encode([ 'success' => FALSE, 'message' => 'Validasi gagal!', 'errors' => $errors ]);
            return;
        }

        // Jika validasi lolos, simpan ke database
        $result = $this->permintaanModel->createPermintaan($catatan, $items, $_SESSION['user_id']);

        if ($result['success'])
        {
            echo json_encode([ 'success' => TRUE, 'message' => 'Permintaan berhasil diajukan!' ]);
        } else
        {
            http_response_code(500); // Internal Server Error
            echo json_encode([ 'success' => FALSE, 'message' => $result['message'] ]);
        }
    }

}