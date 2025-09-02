<?php

require_once APP_PATH . '/core/Controller.php';

class Hakakses extends Controller
{

    private $hakAksesModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !has_permission('role_management_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses ke halaman ini.' ]);
        }
        $this->hakAksesModel = $this->model('hakakses', 'Hakakses_model');
    }

    public function index()
    {

        $data['title']     = 'Manajemen Hak Akses';
        $data['js_module'] = 'hakakses';
        $data['roles']     = $this->hakAksesModel->getAllRoles();
        $this->view('hakakses', 'index_view', $data);
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
            case 'getPermissions':
                $id_role = $_GET['id_role'] ?? 0;
                $data = $this->hakAksesModel->getPermissionsByRole((int) $id_role);
                echo json_encode([ 'success' => TRUE, 'data' => $data ]);
                break;

            case 'updatePermissions':
                if (!has_permission('role_management_update'))
                {
                    http_response_code(403);
                    echo json_encode([ 'success' => FALSE, 'message' => 'Akses ditolak.' ]);
                    return;
                }
                $input = json_decode(file_get_contents('php://input'), TRUE);
                $id_role = $input['id_role'] ?? 0;
                $permissions = $input['permissions'] ?? [];
                $result = $this->hakAksesModel->updateRolePermissions((int) $id_role, $permissions);
                echo json_encode($result);
                break;
        }
    }

}