<?php
require_once APP_PATH . '/core/Controller.php';

class Kartustok extends Controller {
    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect('/dashboard');
        }
    }

    public function index($encrypted_id) {
        $id = $this->encryption->decrypt($encrypted_id);
        if (!$id) die('ID barang tidak valid.');

        $stokModel = $this->model('kartustok', 'Stok_model');
        $barangModel = $this->model('barang', 'Barang_model');

        $data['barang'] = $barangModel->getById($id);
        if (!$data['barang']) die('Barang tidak ditemukan.');

        $data['title'] = 'Kartu Stok: ' . $data['barang']['nama_barang'];
        $data['logs'] = $stokModel->getLogByBarangId($id);

        $this->view('kartustok', 'index_view', $data);
    }
}
?>