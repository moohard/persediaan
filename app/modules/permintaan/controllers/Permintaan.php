<?php
require_once APP_PATH . '/core/Controller.php';

class Permintaan extends Controller
    {
    private $permintaanModel;

    public function __construct()
        {
        parent::__construct();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth');
            }
        $this->permintaanModel = $this->model('permintaan', 'Permintaan_model');
        }

    public function index()
        {
        $data['title']     = 'Daftar Permintaan Barang';
        $data['js_module'] = 'permintaan';

        $barangModel         = $this->model('barang', 'Barang_model');
        $data['barang_list'] = $barangModel->getAllActive();

        $this->view('permintaan', 'index_view', $data);
        }

    public function api($method = '', $param = '')
        {
        header('Content-Type: application/json');

        $is_ajax    = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$is_ajax || empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            http_response_code(403);
            echo json_encode(['success' => FALSE, 'message' => 'Akses tidak sah.']);
            return;
            }

        $input = json_decode(file_get_contents('php://input'), TRUE);

        switch ($method) {
            case 'getAll':
                $permintaan = $this->permintaanModel->getAllPermintaan();
                foreach ($permintaan as &$item) {
                    $item['id_permintaan_encrypted'] = $this->encryption->encrypt($item['id_permintaan']);
                    }
                echo json_encode(['success' => TRUE, 'data' => $permintaan]);
                break;

            case 'getDetail':
                $id = $this->encryption->decrypt($param);
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(['success' => FALSE, 'message' => 'ID Permintaan tidak valid.']);
                    return;
                    }
                $detail = $this->permintaanModel->getDetailById($id);
                echo json_encode(['success' => TRUE, 'data' => $detail]);
                break;

            case 'store':
                $this->store($input);
                break;

            case 'process':
                $this->process($input);
                break;
            }
        }

    private function store($input)
        {
        $catatan         = $input['catatan_pemohon'] ?? '';
        $items           = $input['items'] ?? [];
        $tipe_permintaan = $input['tipe_permintaan'] ?? 'stok';
        $errors          = $this->validate_permintaan($catatan, $items, $tipe_permintaan);

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['success' => FALSE, 'message' => 'Validasi gagal!', 'errors' => $errors]);
            return;
            }

        $result = $this->permintaanModel->createPermintaan($catatan, $items, $_SESSION['user_id'], $tipe_permintaan);

        if ($result['success']) {
            echo json_encode(['success' => TRUE, 'message' => 'Permintaan berhasil diajukan!']);
            }
        else {
            http_response_code(500);
            echo json_encode(['success' => FALSE, 'message' => $result['message']]);
            }
        }

    private function process($input)
        {
        if ($_SESSION['role'] !== 'pimpinan') {
            http_response_code(403);
            echo json_encode(['success' => FALSE, 'message' => 'Hanya pimpinan yang dapat memproses permintaan.']);
            return;
            }

        $id      = $this->encryption->decrypt($input['id']);
        $action  = $input['action'];
        $catatan = $input['catatan_penyetuju'] ?? '';
        $items   = $input['items'] ?? [];

        if (!$id || !in_array($action, ['approve', 'reject'])) {
            http_response_code(400);
            echo json_encode(['success' => FALSE, 'message' => 'Data tidak valid.']);
            return;
            }

        $result = $this->permintaanModel->processPermintaan($id, $action, $catatan, $_SESSION['user_id'], $items);

        if ($result['success']) {
            echo json_encode(['success' => TRUE, 'message' => 'Permintaan berhasil diproses.']);
            }
        else {
            http_response_code(500);
            echo json_encode(['success' => FALSE, 'message' => $result['message']]);
            }
        }

    private function validate_permintaan($catatan, $items, $tipe)
        {
        $errors = [];
        if (empty($catatan)) $errors[] = 'Catatan atau keperluan harus diisi.';
        if (empty($items) || !is_array($items)) {
            $errors[] = 'Minimal harus ada satu barang yang diminta.';
            }
        else {
            $barangModel  = $this->model('barang', 'Barang_model');
            $unique_items = [];
            foreach ($items as $index => $item) {
                if ($item['is_custom']) {
                    if (empty($item['nama_barang_custom'])) {
                        $errors[] = 'Nama barang baru pada baris #' . ($index + 1) . ' tidak boleh kosong.';
                        }
                    }
                else {
                    if (empty($item['id_barang']) || !is_numeric($item['id_barang'])) {
                        $errors[] = 'Barang pada baris #' . ($index + 1) . ' tidak valid.';
                        }
                    if (in_array($item['id_barang'], $unique_items)) {
                        $errors[] = 'Barang yang sama tidak boleh diminta lebih dari sekali.';
                        }
                    $unique_items[] = $item['id_barang'];
                    }

                if (empty($item['jumlah']) || !is_numeric($item['jumlah']) || $item['jumlah'] < 1) {
                    $errors[] = 'Jumlah untuk barang pada baris #' . ($index + 1) . ' harus diisi dan minimal 1.';
                    }

                if ($tipe === 'stok' && !$item['is_custom']) {
                    $barang = $barangModel->getById($item['id_barang']);
                    if ($barang && $item['jumlah'] > $barang['stok_saat_ini']) {
                        $errors[] = "Stok untuk '{$barang['nama_barang']}' tidak mencukupi (tersisa: {$barang['stok_saat_ini']}).";
                        }
                    }
                }
            }
        return $errors;
        }
    }