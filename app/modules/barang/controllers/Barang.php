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
        $data['barang']    = $this->barangModel->getAll();
        $data['js_module'] = 'barang';
        $this->view('barang', 'index_view', $data);
    }

    public function create()
    {

        $data['title'] = 'Tambah Barang Baru';
        $this->view('barang', 'create_view', $data);
    }

    public function store()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/barang');
        verify_csrf_token();

        $data = [
            'kode_barang'  => $_POST['kode_barang'],
            'nama_barang'  => $_POST['nama_barang'],
            'jenis_barang' => $_POST['jenis_barang'],
            'stok'         => (int) $_POST['stok']
        ];

        if ($this->barangModel->create($data))
        {
            set_flash_message('success', 'Data barang berhasil ditambahkan.');
        } else
        {
            set_flash_message('danger', 'Gagal menambahkan data barang.');
        }
        $this->redirect('/barang');
    }

    public function edit($encrypted_id)
    {

        $id = $this->encryption->decrypt($encrypted_id);
        if (!$id) die('ID tidak valid.');

        $data['title'] = 'Edit Data Barang';
        $data['item']  = $this->barangModel->getById($id);
        if (!$data['item']) die('Barang tidak ditemukan.');

        $this->view('barang', 'edit_view', $data);
    }

    public function update()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/barang');
        verify_csrf_token();

        $id = $this->encryption->decrypt($_POST['id']);
        if (!$id) die('ID tidak valid.');

        $data = [
            'kode_barang'  => $_POST['kode_barang'],
            'nama_barang'  => $_POST['nama_barang'],
            'jenis_barang' => $_POST['jenis_barang'],
            'stok'         => (int) $_POST['stok']
        ];

        if ($this->barangModel->update($id, $data))
        {
            set_flash_message('success', 'Data barang berhasil diperbarui.');
        } else
        {
            set_flash_message('danger', 'Gagal memperbarui data barang.');
        }
        $this->redirect('/barang');
    }

    public function destroy()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/barang');
        verify_csrf_token();

        $id = $this->encryption->decrypt($_POST['id']);
        if (!$id) die('ID tidak valid.');

        if ($this->barangModel->delete($id))
        {
            set_flash_message('success', 'Data barang berhasil dihapus.');
        } else
        {
            set_flash_message('danger', 'Gagal menghapus data barang.');
        }
        $this->redirect('/barang');
    }

}