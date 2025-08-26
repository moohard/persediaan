<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// 1. Muat autoloader Composer (jika ada)
if (file_exists(ROOT_PATH . '/vendor/autoload.php'))
{
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// 2. Muat variabel dari file .env ke dalam sistem
if (class_exists('Dotenv\Dotenv'))
{
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// 3. Atur lingkungan berdasarkan variabel dari .env atau default
define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'production');
if (ENVIRONMENT == 'development')
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else
{
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Muat file keamanan SEBELUM session_start()
require_once APP_PATH . '/core/Security.php';
session_start();


// Muat file konfigurasi dan router
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/core/Router.php';

// Inisialisasi dan jalankan router
$router = new Router();
$router->dispatch();