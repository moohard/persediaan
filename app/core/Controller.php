<?php

require_once APP_PATH . '/core/Helper.php';

class Controller
    {

    public $encryption;

    public function __construct()
        {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'developer'])) {
            set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect('/dashboard');
            }
        require_once APP_PATH . '/core/Encryption.php';
        $this->encryption = new Encryption();
        regenerate_session_periodically();
        }

    public function view($module, $view, $data = [])
        {

        $data['encryption'] = $this->encryption;
        extract($data);
        $viewFile = APP_PATH . '/modules/' . $module . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
            }
        else {
            die("View file not found: " . $viewFile);
            }
        }

    public function model($module, $model)
        {

        $modelFile = APP_PATH . '/modules/' . $module . '/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;

            return new $model();
            }
        else {
            die("Model file not found: " . $modelFile);
            }
        }

    protected function redirect($url)
        {

        header('Location: ' . BASE_URL . $url);
        exit();
        }

    }

?>