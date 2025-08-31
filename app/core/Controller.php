<?php

class Controller
{

    protected $encryption;

    public function __construct()
    {

        start_secure_session();
        $this->encryption = new Encryption(ENCRYPTION_KEY);
    }

    public function model($module, $model)
    {

        $model_path = APP_PATH . "/modules/$module/models/$model.php";
        if (file_exists($model_path))
        {
            require_once $model_path;

            return new $model();
        }
        return NULL;
    }

    public function view($module, $view, $data = [])
    {

        $view_path = APP_PATH . "/modules/$module/views/$view.php";
        if (file_exists($view_path))
        {
            extract($data);
            require_once $view_path;
        } else
        {
            die("View tidak ditemukan: $view_path");
        }
    }

    public function redirect($url, $flash_message = NULL)
    {

        if ($flash_message)
        {
            set_flash_message($flash_message['type'], $flash_message['message']);
        }
        header('Location: ' . BASE_URL . $url);
        exit();
    }

}