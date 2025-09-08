<?php

class Router
{

    protected $module = 'auth';

    protected $controller = 'Auth';

    protected $method = 'index';

    protected $params = [];

    protected $routeFound = FALSE;

    public function __construct()
    {

        $this->parseUrl();
    }

    public function parseUrl()
    {

        $url       = $_SERVER['REQUEST_URI'];
        $base_path = parse_url(BASE_URL, PHP_URL_PATH);

        // Remove query string for routing
        $url = strtok($url, '?');

        // Remove base path if exists
        if ($base_path && strpos($url, $base_path) === 0)
        {
            $url = substr($url, strlen($base_path));
        }

        $url          = trim($url, '/');
        $url          = filter_var($url, FILTER_SANITIZE_URL);
        $url_segments = explode('/', $url);

        // Handle API routes specifically
        if ($this->handleApiRoutes($url_segments))
        {
            $this->routeFound = TRUE;

            return;
        }

        // Handle conventional MVC routes
        if ($this->handleConventionalRoutes($url_segments))
        {
            $this->routeFound = TRUE;
            return;
        }

        // If no route found, try default controller
        $this->tryDefaultController();
    }

    protected function handleApiRoutes($segments)
    {

        if (count($segments) >= 3 && $segments[1] === 'api')
        {
            $this->module     = $segments[0];
            $this->controller = ucfirst($segments[0]);
            $this->method     = 'api';
            $this->params     = array_slice($segments, 2);
            return TRUE;
        }
        return FALSE;
    }

    protected function handleConventionalRoutes($segments)
    {

        if (!empty($segments[0]))
        {
            $module_path     = APP_PATH . '/modules/' . $segments[0];
            $controller_name = ucfirst($segments[0]);
            $controller_file = $module_path . '/controllers/' . $controller_name . '.php';

            if (is_dir($module_path) && file_exists($controller_file))
            {
                $this->module     = $segments[0];
                $this->controller = $controller_name;

                if (isset($segments[1]))
                {
                    $this->method = $segments[1];
                    $this->params = array_slice($segments, 2);
                }
                return TRUE;
            }
        }
        return FALSE;
    }

    protected function tryDefaultController()
    {

        $default_controller = APP_PATH . '/modules/auth/controllers/Auth.php';
        if (file_exists($default_controller))
        {
            $this->module     = 'auth';
            $this->controller = 'Auth';
            $this->method     = 'index';
            $this->params     = [];
            $this->routeFound = TRUE;
        }
    }

    public function dispatch()
    {

        if (!$this->routeFound)
        {
            $this->handleNotFound();
            return;
        }

        $controller_path = APP_PATH . '/modules/' . $this->module . '/controllers/' . $this->controller . '.php';

        if (!file_exists($controller_path))
        {
            $this->handleNotFound();
            return;
        }

        require_once $controller_path;

        if (!class_exists($this->controller))
        {
            $this->handleError("Controller class '{$this->controller}' tidak ditemukan.");
            return;
        }

        $controllerInstance = new $this->controller();

        if ($this->method === 'api')
        {
            $this->handleApiMethod($controllerInstance);
        } else
        {
            $this->handleConventionalMethod($controllerInstance);
        }
    }

    protected function handleApiMethod($controllerInstance)
    {

        if (!method_exists($controllerInstance, 'api'))
        {
            $this->handleError("Method 'api' tidak ditemukan di controller '{$this->controller}'.");
            return;
        }

        $apiMethod = $this->params[0] ?? '';
        if (empty($apiMethod))
        {
            $this->handleError("API method tidak ditentukan.");
            return;
        }

        // Kirim parameter tambahan sebagai array
        $apiParams = array_slice($this->params, 1);
        call_user_func_array([ $controllerInstance, 'api' ], array_merge([ $apiMethod ], $apiParams));
    }

    protected function handleConventionalMethod($controllerInstance)
    {

        if (!method_exists($controllerInstance, $this->method))
        {
            $this->handleError("Method '{$this->method}' tidak ditemukan di controller '{$this->controller}'.");
            return;
        }

        call_user_func_array([ $controllerInstance, $this->method ], $this->params);
    }

    protected function handleNotFound()
    {
        http_response_code(404);
        if ($this->isAjaxRequest())
        {
            echo json_encode([ 'success' => FALSE, 'message' => 'Endpoint tidak ditemukan' ]);
        } else
        {
            echo "404 - Halaman tidak ditemukan";
        }
    }

    protected function handleError($message)
    {

        error_log($message);
        http_response_code(500);
        if ($this->isAjaxRequest())
        {
            echo json_encode([ 'success' => FALSE, 'message' => 'Internal Server Error' ]);
        } else
        {
            echo "500 - Internal Server Error";
        }
    }

    protected function isAjaxRequest()
    {

        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

}