<?php

require_once APP_PATH . '/core/Controller.php';

class Pengguna extends Controller
{

    private $penggunaModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !has_permission('user_management_view'))
        {
            $this->redirect('/dashboard', [ 'type' => 'danger', 'message' => 'Anda tidak memiliki akses ke halaman ini.' ]);
        }
        $this->penggunaModel = $this->model('pengguna', 'Pengguna_model');
    }

    public function index()
    {

        $data['title']     = 'Manajemen Pengguna';
        $data['js_module'] = 'pengguna';
        $this->view('pengguna', 'index_view', $data);
    }

    public function api($method = '', $param = '')
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

        $input = json_decode(file_get_contents('php://input'), TRUE);

        switch ($method)
        {
            case 'getAll':
                if (!has_permission('user_management_view'))
                { /* ... tolak akses ... */
                    return;
                }
                $users = $this->penggunaModel->getAllUsers();
                foreach ($users as &$user)
                {
                    $user['id_pengguna_encrypted'] = $this->encryption->encrypt($user['id_pengguna']);
                }
                echo json_encode([ 'success' => TRUE, 'data' => $users ]);
                break;

            case 'getById':
                if (!has_permission('user_management_update'))
                { /* ... tolak akses ... */
                    return;
                }
                $id = $this->encryption->decrypt($param);
                $user = $this->penggunaModel->getUserById($id);
                echo json_encode([ 'success' => TRUE, 'data' => $user ]);
                break;

            case 'getRoles': // Untuk mengisi dropdown
                $roles = $this->penggunaModel->getAllRoles();
                echo json_encode([ 'success' => TRUE, 'data' => $roles ]);
                break;

            case 'getBagian': // Untuk mengisi dropdown
                $bagian = $this->penggunaModel->getAllBagian();
                echo json_encode([ 'success' => TRUE, 'data' => $bagian ]);
                break;

            case 'create':
                if (!has_permission('user_management_create'))
                { /* ... tolak akses ... */
                    return;
                }
                $result = $this->penggunaModel->createUser($input);
                echo json_encode($result);
                break;

            case 'update':
                if (!has_permission('user_management_update'))
                { /* ... tolak akses ... */
                    return;
                }
                $id = $this->encryption->decrypt($input['id_pengguna_encrypted'] ?? NULL);
                $result = $this->penggunaModel->updateUser($id, $input);
                echo json_encode($result);
                break;

            case 'delete':
                if (!has_permission('user_management_delete'))
                { /* ... tolak akses ... */
                    return;
                }
                $id = $this->encryption->decrypt($input['id'] ?? NULL);
                $result = $this->penggunaModel->deleteUser($id);
                echo json_encode($result);
                break;
        }
    }

}