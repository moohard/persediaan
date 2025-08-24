<?php

/*
=====================================================================
 APLIKASI PERSEDIAAN ATK DENGAN STRUKTUR HMVC (FULL SECURITY + AJAX)
=====================================================================

PERUBAHAN UTAMA VERSI INI:
1.  Implementasi Modul "Permintaan Barang" yang fungsional.
2.  Penambahan validasi input di sisi server untuk form permintaan.
3.  Pembaruan skema database dengan tabel dan trigger untuk permintaan.
4.  Penambahan file JavaScript untuk form permintaan yang dinamis.

LANGKAH PERSIAPAN DATABASE:
Jalankan skrip SQL di bawah ini di database Anda terlebih dahulu.

=====================================================================
*/
?>

<!-- ===================================================================== -->
<!-- FILE: database_upgrade.sql (Diperbarui)                               -->
<!-- ===================================================================== -->
<?php

/*
-- 1. Tambah kolom baru di tabel barang (Jika belum ada)
ALTER TABLE `tbl_barang`
ADD `jenis_barang` ENUM('habis_pakai', 'aset') NOT NULL DEFAULT 'habis_pakai' AFTER `nama_barang`;

-- 2. Buat tabel log stok (Jika belum ada)
CREATE TABLE IF NOT EXISTS `tbl_log_stok` (
`id_log` BIGINT AUTO_INCREMENT PRIMARY KEY,
`id_barang` INT NOT NULL,
`jenis_transaksi` ENUM('masuk', 'keluar', 'penyesuaian', 'dihapus') NOT NULL,
`jumlah_ubah` INT NOT NULL,
`stok_sebelum` INT NOT NULL,
`stok_sesudah` INT NOT NULL,
`id_referensi` INT NULL,
`keterangan` VARCHAR(255) NULL,
`id_pengguna_aksi` INT NULL,
`tanggal_log` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang`(`id_barang`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- SKRIP BARU UNTUK MODUL PERMINTAAN
-- =====================================================================

-- 3. Buat tabel header permintaan
CREATE TABLE `tbl_permintaan_atk` (
`id_permintaan` INT AUTO_INCREMENT PRIMARY KEY,
`kode_permintaan` VARCHAR(50) NOT NULL UNIQUE,
`id_pengguna_pemohon` INT NOT NULL,
`tanggal_permintaan` DATE NOT NULL,
`status_permintaan` ENUM('Diajukan', 'Disetujui', 'Ditolak', 'Selesai') NOT NULL DEFAULT 'Diajukan',
`catatan_pemohon` TEXT NULL,
`id_pengguna_penyetuju` INT NULL,
`tanggal_diproses` DATETIME NULL,
`catatan_penyetuju` TEXT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`id_pengguna_pemohon`) REFERENCES `tbl_pengguna`(`id_pengguna`),
FOREIGN KEY (`id_pengguna_penyetuju`) REFERENCES `tbl_pengguna`(`id_pengguna`)
) ENGINE=InnoDB;

-- 4. Buat tabel detail permintaan
CREATE TABLE `tbl_detail_permintaan_atk` (
`id_detail_permintaan` INT AUTO_INCREMENT PRIMARY KEY,
`id_permintaan` INT NOT NULL,
`id_barang` INT NOT NULL,
`jumlah_diminta` INT NOT NULL,
`jumlah_disetujui` INT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`id_permintaan`) REFERENCES `tbl_permintaan_atk`(`id_permintaan`) ON DELETE CASCADE,
FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang`(`id_barang`)
) ENGINE=InnoDB;

-- 5. Buat tabel barang keluar
CREATE TABLE `tbl_barang_keluar` (
`id_barang_keluar` INT AUTO_INCREMENT PRIMARY KEY,
`id_detail_permintaan` INT NOT NULL,
`jumlah_keluar` INT NOT NULL,
`tanggal_keluar` DATE NOT NULL,
`id_admin_gudang` INT NOT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (`id_detail_permintaan`) REFERENCES `tbl_detail_permintaan_atk`(`id_detail_permintaan`),
FOREIGN KEY (`id_admin_gudang`) REFERENCES `tbl_pengguna`(`id_pengguna`)
) ENGINE=InnoDB;

-- 6. Buat Trigger untuk barang keluar
DELIMITER $$
CREATE TRIGGER after_barang_keluar_insert
AFTER INSERT ON tbl_barang_keluar
FOR EACH ROW
BEGIN
DECLARE v_id_barang INT;
DECLARE v_stok_sebelum INT;

-- Ambil id_barang dari detail permintaan
SELECT id_barang INTO v_id_barang 
FROM tbl_detail_permintaan_atk 
WHERE id_detail_permintaan = NEW.id_detail_permintaan;

-- Ambil stok saat ini
SELECT stok_saat_ini INTO v_stok_sebelum FROM tbl_barang WHERE id_barang = v_id_barang;

-- Update stok di tabel barang
UPDATE tbl_barang
SET stok_saat_ini = stok_saat_ini - NEW.jumlah_keluar
WHERE id_barang = v_id_barang;

-- Masukkan catatan ke tabel log stok
INSERT INTO tbl_log_stok (id_barang, jenis_transaksi, jumlah_ubah, stok_sebelum, stok_sesudah, id_referensi, keterangan, id_pengguna_aksi)
VALUES (v_id_barang, 'keluar', -NEW.jumlah_keluar, v_stok_sebelum, (v_stok_sebelum - NEW.jumlah_keluar), NEW.id_detail_permintaan, 'Pengeluaran barang via permintaan', NEW.id_admin_gudang);
END$$
DELIMITER ;

*/ ?>


<!-- ===================================================================== -->
<!-- FILE: .env (Tidak ada perubahan)                                      -->
<!-- ===================================================================== -->
# --- Konfigurasi Aplikasi ---
# Ganti menjadi 'production' saat aplikasi live
APP_ENV=development
BASE_URL="http://localhost:8000" # Ganti dengan URL development Anda

# --- Konfigurasi Database ---
DB_HOST=localhost
DB_NAME=db_atk_pa_penajam
DB_USER=root
DB_PASS=""

# --- Kunci Keamanan ---
# Ganti dengan string acak 16 karakter
ENCRYPTION_KEY="YourSecret16CharKey"


<!-- ===================================================================== -->
<!-- FILE: public/index.php (Tidak ada perubahan)                          -->
<!-- ===================================================================== -->
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
?>


<!-- ===================================================================== -->
<!-- FILE: app/config/config.php (Tidak ada perubahan)                     -->
<!-- ===================================================================== -->
<?php

// File ini sekarang membaca variabel yang sudah dimuat.
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost');
define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? 'DefaultSecretKey16');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300);
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
?>


<!-- ===================================================================== -->
<!-- FILE: app/core/Router.php (Tidak ada perubahan)                       -->
<!-- ===================================================================== -->
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

        $base_path = rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/');
        if ($base_path && strpos($url_path, $base_path) === 0)
        {
            $url_path = substr($url_path, strlen($base_path));
        }

        $url = explode('/', filter_var(trim($url_path, '/'), FILTER_SANITIZE_URL));

        if (!empty($url[0]) && is_dir(APP_PATH . '/modules/' . $url[0]))
        {
            $this->module = $url[0];
            array_shift($url);
        }

        if (!empty($url[0]) && file_exists(APP_PATH . '/modules/' . $this->module . '/controllers/' . ucfirst($url[0]) . '.php'))
        {
            $this->controller = ucfirst($url[0]);
            array_shift($url);
        } else
        {
            if (file_exists(APP_PATH . '/modules/' . $this->module . '/controllers/' . ucfirst($this->module) . '.php'))
            {
                $this->controller = ucfirst($this->module);
            }
        }

        if (!empty($url[0]))
        {
            $this->method = $url[0];
            array_shift($url);
        }

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


<!-- ===================================================================== -->
<!-- FILE: app/core/Controller.php (Tidak ada perubahan)                   -->
<!-- ===================================================================== -->
<?php

require_once APP_PATH . '/core/Helper.php';

class Controller
{

    public $encryption;

    public function __construct()
    {

        require_once APP_PATH . '/core/Encryption.php';
        $this->encryption = new Encryption();
        regenerate_session_periodically();
    }

    public function view($module, $view, $data = [])
    {

        $data['encryption'] = $this->encryption;
        extract($data);
        $viewFile = APP_PATH . '/modules/' . $module . '/views/' . $view . '.php';
        if (file_exists($viewFile))
        {
            require_once $viewFile;
        } else
        {
            die("View file not found: " . $viewFile);
        }
    }

    public function model($module, $model)
    {

        $modelFile = APP_PATH . '/modules/' . $module . '/models/' . $model . '.php';
        if (file_exists($modelFile))
        {
            require_once $modelFile;

            return new $model();
        } else
        {
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


<!-- ===================================================================== -->
<!-- FILE: app/core/Model.php (Tidak ada perubahan)                        -->
<!-- ===================================================================== -->
<?php

class Model
{

    protected $db;

    public function __construct()
    {

        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error)
        {
            if (ENVIRONMENT === 'development')
            {
                die("Koneksi database gagal: " . $this->db->connect_error);
            } else
            {
                die("Tidak dapat terhubung ke server database.");
            }
        }
    }

    public function __destruct()
    {

        $this->db->close();
    }

}

?>


<!-- ===================================================================== -->
<!-- FILE: app/core/Helper.php (Tidak ada perubahan)                       -->
<!-- ===================================================================== -->
<?php

function e(?string $string): string
{

    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token()
{

    if (empty($_SESSION['csrf_token']))
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function verify_csrf_token()
{

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))
    {
        die('CSRF token validation failed.');
    }
}

function regenerate_session_periodically()
{

    $interval = 1800;
    if (!isset($_SESSION['last_regeneration']))
    {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > $interval)
    {
        session_regenerate_id(TRUE);
        $_SESSION['last_regeneration'] = time();
    }
}

function set_flash_message($type, $message)
{

    $_SESSION['flash_message'] = [ 'type' => $type, 'message' => $message ];
}

function display_flash_message()
{

    if (isset($_SESSION['flash_message']))
    {
        $flash = $_SESSION['flash_message'];
        echo '<div class="alert alert-' . e($flash['type']) . ' alert-dismissible fade show" role="alert">';
        echo e($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['flash_message']);
    }
}

?>


<!-- ===================================================================== -->
<!-- FILE: app/core/Security.php (Tidak ada perubahan)                     -->
<!-- ===================================================================== -->
<?php

ini_set('session.use_only_cookies', 1);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => (isset($_SERVER['HTTPS'])),
    'httponly' => TRUE,
    'samesite' => 'Lax',
]);
?>


<!-- ===================================================================== -->
<!-- FILE: app/core/Encryption.php (Tidak ada perubahan)                   -->
<!-- ===================================================================== -->
<?php

class Encryption
{

    private const CIPHER = 'aes-128-cbc';

    private $key;

    public function __construct()
    {

        $this->key = ENCRYPTION_KEY;
    }

    public function encrypt($data)
    {

        $iv_length  = openssl_cipher_iv_length(self::CIPHER);
        $iv         = openssl_random_pseudo_bytes($iv_length);
        $ciphertext = openssl_encrypt($data, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
        $encrypted  = base64_encode($iv . $ciphertext);
        return strtr($encrypted, '+/', '-_');
    }

    public function decrypt($encrypted_data)
    {

        $data         = strtr($encrypted_data, '-_', '+/');
        $decoded_data = base64_decode($data, TRUE);
        if ($decoded_data === FALSE) return FALSE;
        $iv_length  = openssl_cipher_iv_length(self::CIPHER);
        $iv         = substr($decoded_data, 0, $iv_length);
        $ciphertext = substr($decoded_data, $iv_length);
        if (strlen($iv) < $iv_length) return FALSE;
        return openssl_decrypt($ciphertext, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
    }

}

?>


<!-- ===================================================================== -->
<!-- FILE: app/views/templates/header.php (Tidak ada perubahan)            -->
<!-- ===================================================================== -->
<?php

regenerate_session_periodically();
generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e($_SESSION['csrf_token'] ?? ''); ?>">
    <title><?php echo e($title ?? 'Sistem Persediaan ATK'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/dashboard"><i class="bi bi-box-seam-fill"></i> ATK PA Penajam</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">Dashboard</a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/barang">Data Barang</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/permintaan">Permintaan</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo e($_SESSION['nama_lengkap']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/auth/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php display_flash_message(); ?>


        <!-- ===================================================================== -->
        <!-- FILE: app/views/templates/footer.php (Diperbarui)                     -->
        <!-- ===================================================================== -->
    </div> <!-- .container -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <!-- PERUBAHAN: Menambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>

    <!-- PERUBAHAN: Memanggil JS utama dan JS spesifik per modul -->
    <script src="/js/main.js"></script>
    <?php if (isset($js_module)) : ?>
        <?php $js_file_path = ROOT_PATH . '/public/js/modules/' . $js_module . '.js'; ?>
        <?php if (file_exists($js_file_path)) : ?>
            <script src="/js/modules/<?php echo e($js_module); ?>.js"></script>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>


<!-- ===================================================================== -->
<!-- MODUL AUTH (Tidak ada perubahan)                                      -->
<!-- ===================================================================== -->
<!-- FILE: app/modules/auth/controllers/Auth.php -->
<?php

require_once APP_PATH . '/core/Controller.php';

class Auth extends Controller
{

    public function __construct()
    {

        parent::__construct();
        if (isset($_SESSION['user_id']) && strpos($_SERVER['REQUEST_URI'], 'logout') === FALSE)
        {
            $this->redirect('/dashboard');
        }
    }

    public function index()
    {

        generate_csrf_token();
        $data['title'] = 'Login';
        $data['error'] = $_SESSION['error_message'] ?? NULL;
        unset($_SESSION['error_message']);

        if (isset($_SESSION['lockout_time']) && time() - $_SESSION['lockout_time'] < LOCKOUT_TIME)
        {
            $remaining_time    = LOCKOUT_TIME - (time() - $_SESSION['lockout_time']);
            $data['error']     = "Terlalu banyak percobaan. Coba lagi dalam " . ceil($remaining_time / 60) . " menit.";
            $data['is_locked'] = TRUE;
        } else
        {
            unset($_SESSION['lockout_time'], $_SESSION['login_attempts']);
            $data['is_locked'] = FALSE;
        }

        $this->view('auth', 'login_view', $data);
    }

    public function process_login()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/auth');

        verify_csrf_token();

        $username = $_POST['username'];
        $password = $_POST['password'];

        $authModel = $this->model('auth', 'Auth_model');
        $user      = $authModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password']))
        {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama'];
            $_SESSION['role']         = $user['role'];
            session_regenerate_id(TRUE);

            unset($_SESSION['login_attempts'], $_SESSION['lockout_time']);

            $this->redirect('/dashboard');
        } else
        {
            if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempts']++;

            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS)
            {
                $_SESSION['lockout_time']  = time();
                $_SESSION['error_message'] = "Akun Anda diblokir sementara.";
            } else
            {
                $remaining                 = MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts'];
                $_SESSION['error_message'] = "Username atau password salah. Sisa percobaan: $remaining.";
            }
            $this->redirect('/auth');
        }
    }

    public function logout()
    {

        session_unset();
        session_destroy();
        $this->redirect('/auth');
    }

}

?>

<!-- FILE: app/modules/auth/models/Auth_model.php -->
<?php

require_once APP_PATH . '/core/Model.php';

class Auth_model extends Model
{

    public function getUserByUsername($username)
    {

        $users = [
            'admin' => [
                'id'       => 1,
                'nama'     => 'Administrator',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
        ];
        return $users[$username] ?? NULL;
    }

}

?>

<!-- FILE: app/modules/auth/views/login_view.php -->
<?php require_once APP_PATH . '/views/templates/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Login Sistem ATK</h3>
                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>
                <form action="<?php echo BASE_URL; ?>/auth/process_login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" <?php if ($is_locked ?? FALSE) echo 'disabled'; ?>>
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>


<!-- ===================================================================== -->
<!-- MODUL DASHBOARD (Tidak ada perubahan)                                 -->
<!-- ===================================================================== -->
<!-- FILE: app/modules/dashboard/controllers/Dashboard.php -->
<?php

require_once APP_PATH . '/core/Controller.php';

class Dashboard extends Controller
{

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']))
        {
            $this->redirect('/auth');
        }
    }

    public function index()
    {

        $data['title']     = 'Dashboard';
        $data['nama_user'] = $_SESSION['nama_lengkap'];
        $this->view('dashboard', 'dashboard_view', $data);
    }

}

?>

<!-- FILE: app/modules/dashboard/views/dashboard_view.php -->
<?php require_once APP_PATH . '/views/templates/header.php'; ?>
<h2>Selamat Datang, <?php echo e($nama_user); ?>!</h2>
<p>Ini adalah halaman dashboard Anda.</p>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>


<!-- ===================================================================== -->
<!-- MODUL BARANG (Tidak ada perubahan)                                    -->
<!-- ===================================================================== -->

<!-- FILE: app/modules/barang/controllers/Barang.php -->
<?php

require_once APP_PATH . '/core/Controller.php';

class Barang extends Controller
{

    private $barangModel;

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
        {
            set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect('/dashboard');
        }
        $this->barangModel = $this->model('barang', 'Barang_model');
    }

    public function index()
    {

        $data['title']     = 'Manajemen Data Barang';
        $data['js_module'] = 'barang';
        $this->view('barang', 'index_view', $data);
    }

    // Metode API untuk AJAX
    public function api($method = '', $param = '')
    {

        header('Content-Type: application/json');

        $headers = getallheaders();
        if (!isset($headers['X-Csrf-Token']) || !hash_equals($_SESSION['csrf_token'], $headers['X-Csrf-Token']))
        {
            echo json_encode([ 'success' => FALSE, 'message' => 'Invalid CSRF Token' ]);
            return;
        }

        switch ($method)
        {
            case 'getAll':
                $barang = $this->barangModel->getAll();
                foreach ($barang as &$item)
                {
                    $item['id_barang_encrypted'] = $this->encryption->encrypt($item['id_barang']);
                }
                echo json_encode([ 'success' => TRUE, 'data' => $barang ]);
                break;

            case 'getById':
                $id = $this->encryption->decrypt($param);
                $item = $this->barangModel->getById($id);
                echo json_encode([ 'success' => TRUE, 'data' => $item ]);
                break;

            case 'create':
                $data = [
                    'kode_barang'  => $_POST['kode_barang'],
                    'nama_barang'  => $_POST['nama_barang'],
                    'jenis_barang' => $_POST['jenis_barang'],
                    'stok'         => (int) $_POST['stok']
                ];
                if ($this->barangModel->create($data))
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil ditambahkan.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'Gagal menambahkan data.' ]);
                }
                break;

            case 'update':
                $data = [
                    'id'           => $this->encryption->decrypt($_POST['id']),
                    'kode_barang'  => $_POST['kode_barang'],
                    'nama_barang'  => $_POST['nama_barang'],
                    'jenis_barang' => $_POST['jenis_barang'],
                    'stok'         => (int) $_POST['stok']
                ];
                if ($this->barangModel->update($data['id'], $data))
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil diperbarui.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'Gagal memperbarui data.' ]);
                }
                break;

            case 'delete':
                $id = $this->encryption->decrypt($_POST['id']);
                if ($this->barangModel->delete($id))
                {
                    echo json_encode([ 'success' => TRUE, 'message' => 'Data berhasil dihapus.' ]);
                } else
                {
                    echo json_encode([ 'success' => FALSE, 'message' => 'Gagal menghapus data.' ]);
                }
                break;
        }
    }

}

?>

<!-- FILE: app/modules/barang/models/Barang_model.php -->
<?php

require_once APP_PATH . '/core/Model.php';

class Barang_model extends Model
{

    public function getAll()
    {

        $result = $this->db->query("SELECT * FROM tbl_barang ORDER BY nama_barang ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {

        $stmt = $this->db->prepare("SELECT * FROM tbl_barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data)
    {

        $stmt = $this->db->prepare("INSERT INTO tbl_barang (kode_barang, nama_barang, jenis_barang, stok_saat_ini, id_kategori, id_satuan) VALUES (?, ?, ?, ?, 1, 1)");
        $stmt->bind_param("sssi", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {

        $stmt = $this->db->prepare("UPDATE tbl_barang SET kode_barang = ?, nama_barang = ?, jenis_barang = ?, stok_saat_ini = ? WHERE id_barang = ?");
        $stmt->bind_param("sssii", $data['kode_barang'], $data['nama_barang'], $data['jenis_barang'], $data['stok'], $id);
        return $stmt->execute();
    }

    public function delete($id)
    {

        $stmt = $this->db->prepare("DELETE FROM tbl_barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

}

?>

<!-- FILE: app/modules/barang/views/index_view.php -->
<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <button id="btn-add-barang" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Barang</button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Stok</th>
                        <th style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="barang-table-body">
                    <!-- Data akan dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Barang -->
<div class="modal fade" id="barang-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Form Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="barang-form">
                <div class="modal-body">
                    <input type="hidden" name="id" id="barang-id">
                    <div class="mb-3">
                        <label for="kode_barang" class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_barang" class="form-label">Jenis Barang</label>
                        <select class="form-select" id="jenis_barang" name="jenis_barang" required>
                            <option value="habis_pakai">Barang Habis Pakai</option>
                            <option value="aset">Aset</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>


<!-- ===================================================================== -->
<!-- MODUL KARTU STOK (Tidak ada perubahan)                                -->
<!-- ===================================================================== -->

<!-- FILE: app/modules/kartustok/controllers/Kartustok.php -->
<?php

require_once APP_PATH . '/core/Controller.php';

class Kartustok extends Controller
{

    public function __construct()
    {

        parent::__construct();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
        {
            set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect('/dashboard');
        }
    }

    public function index($encrypted_id)
    {

        $id = $this->encryption->decrypt($encrypted_id);
        if (!$id) die('ID barang tidak valid.');

        $stokModel   = $this->model('kartustok', 'Stok_model');
        $barangModel = $this->model('barang', 'Barang_model');

        $data['barang'] = $barangModel->getById($id);
        if (!$data['barang']) die('Barang tidak ditemukan.');

        $data['title'] = 'Kartu Stok: ' . $data['barang']['nama_barang'];
        $data['logs']  = $stokModel->getLogByBarangId($id);

        $this->view('kartustok', 'index_view', $data);
    }

}

?>

<!-- FILE: app/modules/kartustok/models/Stok_model.php -->
<?php

require_once APP_PATH . '/core/Model.php';

class Stok_model extends Model
{

    public function getLogByBarangId($id_barang)
    {

        $stmt = $this->db->prepare("SELECT * FROM tbl_log_stok WHERE id_barang = ? ORDER BY tanggal_log DESC");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}

?>

<!-- FILE: app/modules/kartustok/views/index_view.php -->
<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<h3><?php echo e($title); ?></h3>
<p>
    <strong>Kode Barang:</strong> <?php echo e($barang['kode_barang']); ?> |
    <strong>Stok Saat Ini:</strong> <span
        class="badge bg-primary fs-6"><?php echo e($barang['stok_saat_ini']); ?></span>
</p>

<div class="card">
    <div class="card-body">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                    <th>Perubahan</th>
                    <th>Stok Sebelum</th>
                    <th>Stok Sesudah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?php echo e(date('d M Y, H:i', strtotime($log['tanggal_log']))); ?></td>
                        <td><?php echo e(ucwords($log['jenis_transaksi'])); ?></td>
                        <td>
                            <?php if ($log['jumlah_ubah'] > 0) : ?>
                                <span class="text-success fw-bold">+<?php echo e($log['jumlah_ubah']); ?></span>
                            <?php else : ?>
                                <span class="text-danger fw-bold"><?php echo e($log['jumlah_ubah']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($log['stok_sebelum']); ?></td>
                        <td><?php echo e($log['stok_sesudah']); ?></td>
                        <td><?php echo e($log['keterangan']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    <a href="/barang" class="btn btn-secondary">Kembali ke Daftar Barang</a>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>


<!-- ===================================================================== -->
<!-- FILE JAVASCRIPT BARU                                                  -->
<!-- ===================================================================== -->

<!-- FILE: public/js/main.js (File Baru) -->
// File ini untuk skrip global
$(document).ready(function() {
// Konfigurasi Global AJAX untuk mengirim token CSRF
$.ajaxSetup({
headers: {
'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
}
});
});

<!-- FILE: public/js/modules/barang.js (File Baru) -->
$(document).ready(function() {
const modal = new bootstrap.Modal(document.getElementById('barang-modal'));
const form = $('#barang-form');
const modalTitle = $('#modal-title');

function loadBarang() {
$.ajax({
url: '/barang/api/getAll',
method: 'GET',
dataType: 'json',
success: function(response) {
let html = '';
if (response.data.length > 0) {
response.data.forEach(item => {
html += `
<tr>
    <td>${item.kode_barang}</td>
    <td>${item.nama_barang}</td>
    <td><span class="badge bg-secondary">${item.jenis_barang.replace('_', ' ')}</span></td>
    <td>${item.stok_saat_ini}</td>
    <td>
        <a href="/kartustok/index/${item.id_barang_encrypted}" class="btn btn-sm btn-success">Log</a>
        <button class="btn btn-sm btn-warning btn-edit" data-id="${item.id_barang_encrypted}">Edit</button>
        <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id_barang_encrypted}">Hapus</button>
    </td>
</tr>
`;
});
} else {
html = '<tr>
    <td colspan="5" class="text-center">Belum ada data.</td>
</tr>';
}
$('#barang-table-body').html(html);
}
});
}

$('#btn-add-barang').on('click', function() {
form.trigger('reset');
form.attr('action', '/barang/api/create');
modalTitle.text('Tambah Barang Baru');
$('#barang-id').val('');
modal.show();
});

$('#barang-table-body').on('click', '.btn-edit', function() {
const id = $(this).data('id');
$.ajax({
url: `/barang/api/getById/${id}`,
method: 'GET',
dataType: 'json',
success: function(response) {
if (response.success) {
const item = response.data;
form.trigger('reset');
form.attr('action', '/barang/api/update');
modalTitle.text('Edit Data Barang');
$('#barang-id').val(id);
$('#kode_barang').val(item.kode_barang);
$('#nama_barang').val(item.nama_barang);
$('#jenis_barang').val(item.jenis_barang);
$('#stok').val(item.stok_saat_ini);
modal.show();
}
}
});
});

form.on('submit', function(e) {
e.preventDefault();
const url = $(this).attr('action');
let formData = $(this).serializeArray();
// Tambahkan CSRF Token ke data form
formData.push({name: "csrf_token", value: $('meta[name="csrf-token"]').attr('content')});

$.ajax({
url: url,
method: 'POST',
data: $.param(formData),
dataType: 'json',
success: function(response) {
modal.hide();
if (response.success) {
Swal.fire({
icon: 'success',
title: 'Berhasil!',
text: response.message,
timer: 1500,
showConfirmButton: false
});
loadBarang();
} else {
Swal.fire({
icon: 'error',
title: 'Gagal!',
text: response.message
});
}
}
});
});

$('#barang-table-body').on('click', '.btn-delete', function() {
const id = $(this).data('id');
Swal.fire({
title: 'Anda yakin?',
text: "Data yang dihapus tidak dapat dikembalikan!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#3085d6',
confirmButtonText: 'Ya, hapus!',
cancelButtonText: 'Batal'
}).then((result) => {
if (result.isConfirmed) {
$.ajax({
url: '/barang/api/delete',
method: 'POST',
data: { id: id, csrf_token: $('meta[name="csrf-token"]').attr('content') },
dataType: 'json',
success: function(response) {
if (response.success) {
Swal.fire('Terhapus!', response.message, 'success');
loadBarang();
} else {
Swal.fire('Gagal!', response.message, 'error');
}
}
});
}
});
});

loadBarang();
});