<?php

class Router
{

    protected $module     = 'auth';

    protected $controller = 'Auth';

    protected $method     = 'index';

    protected $params     = [];

    public function __construct()
    {

        $this->parseUrl();
    }

    public function parseUrl()
    {

        $url       = $_SERVER['REQUEST_URI'];
        $base_path = parse_url(BASE_URL, PHP_URL_PATH);
        if ($base_path && strpos($url, $base_path) === 0)
        {
            $url = substr($url, strlen($base_path));
        }
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        // Set Module dan Controller
        if (!empty($url[0]))
        {
            $module_path     = APP_PATH . '/modules/' . $url[0];
            $controller_name = ucfirst($url[0]);
            $controller_file = $module_path . '/controllers/' . $controller_name . '.php';

            if (is_dir($module_path) && file_exists($controller_file))
            {
                $this->module     = $url[0];
                $this->controller = $controller_name;
                unset($url[0]);
            }
        }

        // Set Method
        if (isset($url[1]))
        {
            $controller_path = APP_PATH . '/modules/' . $this->module . '/controllers/' . $this->controller . '.php';
            if (file_exists($controller_path))
            {
                require_once $controller_path;
                if (method_exists($this->controller, $url[1]))
                {
                    $this->method = $url[1];
                    unset($url[1]);
                }
            }
        }

        // Set Params
        $this->params = $url ? array_values($url) : [];
    }

    public function dispatch()
    {

        $controller_path = APP_PATH . '/modules/' . $this->module . '/controllers/' . $this->controller . '.php';

        if (file_exists($controller_path))
        {
            require_once $controller_path;
            $controllerInstance = new $this->controller;

            if (method_exists($controllerInstance, $this->method))
            {
                call_user_func_array([ $controllerInstance, $this->method ], $this->params);
            } else
            {
                die("Method '{$this->method}' tidak ditemukan di controller '{$this->controller}'.");
            }
        } else
        {
            die("Controller '{$this->controller}' tidak ditemukan di modul '{$this->module}'.");
        }
    }

}