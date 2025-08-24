<?php

require_once APP_PATH . '/core/Controller.php';

class Permintaan extends Controller
{

    private $permintaanModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->permintaanModel = $this->model('permintaan', 'Permintaan_model');
    }

    public function index()
    {

        $data['title']      = 'Daftar Permintaan Barang';
        $data['js_module']  = 'permintaan';
        $data['permintaan'] = $this->permintaanModel->getAllPermintaan();

        // PERBAIKAN: Menggunakan method getAllActive() yang benar
        $barangModel         = $this->model('barang', 'Barang_model');
        $data['barang_list'] = $barangModel->getAllActive();

        $this->view('permintaan', 'index_view', $data);
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

        switch ($method)
        {
            case 'store':
                $this->store();
                break;
        }
    }

    private function store()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            http_response_code(405);
            echo json_encode([ 'success' => FALSE, 'message' => 'Method not allowed.' ]);
            return;
        }

        $json_data = file_get_contents('php://input');
        $post_data = json_decode($json_data, TRUE);

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
            http_response_code(422);
            echo json_encode([ 'success' => FALSE, 'message' => 'Validasi gagal!', 'errors' => $errors ]);
            return;
        }

        $result = $this->permintaanModel->createPermintaan($catatan, $items, $_SESSION['user_id']);

        if ($result['success'])
        {
            echo json_encode([ 'success' => TRUE, 'message' => 'Permintaan berhasil diajukan!' ]);
        } else
        {
            http_response_code(500);
            echo json_encode([ 'success' => FALSE, 'message' => $result['message'] ]);
        }
    }

}