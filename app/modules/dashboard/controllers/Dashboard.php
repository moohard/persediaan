<?php

require_once APP_PATH . '/core/Controller.php';

class Dashboard extends Controller
{

    private $dashboardModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
        $this->dashboardModel = $this->model('dashboard', 'Dashboard_model');
    }

    public function index()
    {

        $data['title']     = 'Dashboard';
        $data['js_module'] = 'dashboard';
        $this->view('dashboard', 'index_view', $data);
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

        if ($method === 'getStats')
        {
            if (!has_permission('dashboard_view_stats'))
            {
                http_response_code(403);
                echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                return;
            }
            $stats = [
                'summary' => $this->dashboardModel->getSummaryStats(),
                'chart'   => $this->dashboardModel->getMonthlyUsageChartData(),
            ];
            echo json_encode([ 'success' => TRUE, 'data' => $stats ]);
        }
    }

}