<?php

require_once APP_PATH . '/core/Controller.php';

class Laporan extends Controller
{

    private $laporanModel;

    private $barangModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !has_permission('laporan_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses ke halaman ini.' ]);
        }
        $this->laporanModel = $this->model('laporan', 'Laporan_model');
        $this->barangModel  = $this->model('barang', 'Barang_model'); // Inisialisasi barangModel

    }

    public function index()
    {

        $data['title']       = 'Laporan';
        $data['js_module']   = 'laporan';
        $data['barang_list'] = $this->barangModel->getAllActive();
        $this->view('laporan', 'index_view', $data);

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

        try
        {
            switch ($method)
            {
                case 'getStokBarang':
                    if (!has_permission('laporan_view'))
                    { /* ... tolak akses ... */
                        return;
                    }
                    $data = $this->laporanModel->getAllStock();
                    echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                    break;
                case 'getKartuStok':
                    if (!has_permission('laporan_kartu_stok_view'))
                    {
                        http_response_code(403);
                        echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);

                        return;
                    }
                    $id_barang = $_GET['id_barang'] ?? 0;

                    $data = $this->laporanModel->getStockCard((int) $id_barang);
                    echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                    break;
            }
        } catch (Exception $e)
        {
            // Clean buffer and return error
            http_response_code(500);
            echo json_encode([
                'success' => FALSE,
                'message' => 'Error: ' . $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

    }

}