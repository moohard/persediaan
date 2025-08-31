<?php

require_once APP_PATH . '/core/Controller.php';

class Dashboard extends Controller
{

    public function __construct()
    {

        parent::__construct();
        // PERBAIKAN: Pindahkan cek login ke constructor untuk melindungi semua method
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
    }

    public function index()
    {

        $data['title']     = 'Dashboard';
        $data['nama_user'] = $_SESSION['nama'];
        
        $this->view('dashboard', 'dashboard_view', $data);
    }

}

?>