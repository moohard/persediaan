<?php

require_once APP_PATH . '/core/Controller.php';

class Laporan extends Controller
{

    private $laporanModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !has_permission('laporan_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses ke halaman ini.' ]);
        }
        $this->laporanModel = $this->model('laporan', 'Laporan_model');
    }

    public function index()
    {

        $data['title']       = 'Laporan';
        $data['js_module']   = 'laporan';
        $barangModel         = $this->model('barang', 'Barang_model');
        $data['barang_list'] = $barangModel->getAllActive();
        $this->view('laporan', 'index_view', $data);
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

        switch ($method)
        {
            case 'getStokBarang':
                if (!has_permission('laporan_view'))
                {
                    http_response_code(403);
                    return;
                }
                $data = $this->laporanModel->getAllStock();
                echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                break;

            case 'getKartuStok':
                if (!has_permission('laporan_kartu_stok_view'))
                {
                    http_response_code(403);
                    return;
                }
                $id_barang = $_GET['id_barang'] ?? 0;
                $data = $this->laporanModel->getStockCard((int) $id_barang);
                echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                break;

            case 'getPermintaanReport':
                if (!has_permission('laporan_permintaan_view'))
                {
                    http_response_code(403);
                    return;
                }
                $filters = [
                    'start_date' => $_GET['start_date'] ?? NULL,
                    'end_date'   => $_GET['end_date'] ?? NULL,
                    'status'     => $_GET['status'] ?? 'semua',
                ];
                $data = $this->laporanModel->getPermintaanReport($filters);
                echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                break;

            case 'getPembelianReport':
                if (!has_permission('laporan_pembelian_view'))
                {
                    http_response_code(403);
                    return;
                }
                $filters = [
                    'start_date' => $_GET['start_date'] ?? NULL,
                    'end_date'   => $_GET['end_date'] ?? NULL,
                    'status'     => $_GET['status'] ?? 'semua',
                ];
                $data = $this->laporanModel->getPembelianReport($filters);
                echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                break;
        }
    }

}