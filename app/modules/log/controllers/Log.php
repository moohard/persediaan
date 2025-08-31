<?php

require_once APP_PATH . '/core/Controller.php';

class Log extends Controller
{

    private $log_file;

    public function __construct()
    {

        parent::__construct();
        // Fitur ini hanya untuk admin di mode development
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->log_file = ROOT_PATH . '/logs/query_log_' . date('Y-m-d') . '.log';
    }

    public function index()
    {

        if (!has_permission('log_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses.' ]);
        }
        $data['title']       = 'Query Log Hari Ini';
        $data['log_content'] = '';
        if (file_exists($this->log_file))
        {
            // Baca file dari belakang agar log terbaru di atas
            $data['log_content'] = file_get_contents($this->log_file);
        }
        $this->view('log', 'index_view', $data);
    }

    public function clear()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            verify_csrf_token();
            if (file_exists($this->log_file))
            {
                unlink($this->log_file);
                set_flash_message('success', 'File log berhasil dibersihkan.');
            }
        }
        $this->redirect('/log');
    }

}