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

        $url_path = '/';
        if (isset($_SERVER['REQUEST_URI']))
        {
            $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        // Hapus base path dari URL jika aplikasi berada di subfolder
        $base_path = parse_url(BASE_URL, PHP_URL_PATH);
        if ($base_path && strpos($url_path, $base_path) === 0)
        {
            $url_path = substr($url_path, strlen($base_path));
        }

        $url = explode('/', filter_var(trim($url_path, '/'), FILTER_SANITIZE_URL));

        // 1. Tentukan Modul
        if (!empty($url[0]) && is_dir(APP_PATH . '/modules/' . $url[0]))
        {
            $this->module = $url[0];
            array_shift($url);
        }

        // 2. Tentukan Controller
        if (!empty($url[0]) && file_exists(APP_PATH . '/modules/' . $this->module . '/controllers/' . ucfirst($url[0]) . '.php'))
        {
            $this->controller = ucfirst($url[0]);
            array_shift($url);
        } else
        {
            // Fallback ke controller default untuk modul (misal: /barang akan memuat controller Barang)
            if (file_exists(APP_PATH . '/modules/' . $this->module . '/controllers/' . ucfirst($this->module) . '.php'))
            {
                $this->controller = ucfirst($this->module);
            }
        }

        // 3. Tentukan Method
        if (!empty($url[0]))
        {
            $this->method = $url[0];
            array_shift($url);
        }

        // 4. Sisa URL adalah parameter
        $this->params = $url ? array_values($url) : [];
    }

    public function dispatch()
    {

        $controllerFile = APP_PATH . '/modules/' . $this->module . '/controllers/' . $this->controller . '.php';

        if (file_exists($controllerFile))
        {
            require_once $controllerFile;

            if (class_exists($this->controller))
            {
                $controllerInstance = new $this->controller;

                if (method_exists($controllerInstance, $this->method))
                {
                    call_user_func_array([ $controllerInstance, $this->method ], $this->params);
                } else
                {
                    die("Method '{$this->method}' not found in controller '{$this->controller}'.");
                }
            } else
            {
                die("Class '{$this->controller}' not found in file '{$controllerFile}'.");
            }
        } else
        {
            die("Controller file '{$controllerFile}' not found for module '{$this->module}'.");
        }
    }

}

?>