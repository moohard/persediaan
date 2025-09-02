<?php

require_once APP_PATH . '/core/Controller.php';

class Notifikasi extends Controller
{

    private $notifModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || !has_permission('notifikasi_view'))
        {
            // Jangan redirect, cukup hentikan jika akses tidak sah
            http_response_code(403);
            exit();
        }
        $this->notifModel = $this->model('notifikasi', 'Notifikasi_model');
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

        switch ($method)
        {
            case 'getUnread':
                $notifications = $this->notifModel->getUnreadNotifications($_SESSION['user_id']);
                echo json_encode([ 'success' => TRUE, 'data' => $notifications ]);
                break;
            case 'markAsRead':
                $input = json_decode(file_get_contents('php://input'), TRUE);
                $notif_id = $input['id'] ?? NULL;
                $result = $this->notifModel->markAsRead($_SESSION['user_id'], $notif_id);
                echo json_encode($result);
                break;
        }
    }

}