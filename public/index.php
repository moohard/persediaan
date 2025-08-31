<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

// Muat autoloader Composer
if (file_exists(ROOT_PATH . '/vendor/autoload.php'))
{
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// Muat variabel dari file .env
if (class_exists('Dotenv\Dotenv'))
{
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// --- PENGATURAN LINGKUNGAN (ENVIRONMENT) ---
define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'production');
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost');
define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? 'ganti_dengan_kunci_rahasia_anda');

// [PERBAIKAN] Mendefinisikan konstanta database dari .env
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Pengaturan Error Reporting
if (ENVIRONMENT === 'development')
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else
{
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Muat file inti
require_once APP_PATH . '/core/Helper.php';
require_once APP_PATH . '/core/Encryption.php';
require_once APP_PATH . '/core/Model.php'; // Model harus dimuat sebelum Controller
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Jalankan Router
$router = new Router();
$router->dispatch();