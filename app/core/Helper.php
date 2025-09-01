<?php

/**
 * Memulai sesi dengan pengaturan keamanan dan memastikan token CSRF ada.
 */
function start_secure_session()
{

    if (session_status() === PHP_SESSION_NONE)
    {
        $cookieParams = [
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => TRUE,
            'samesite' => 'Lax',
        ];
        session_set_cookie_params($cookieParams);
        session_start();
    }

    // [PERBAIKAN] Token CSRF sekarang dibuat secara otomatis bersamaan dengan sesi.
    if (empty($_SESSION['csrf_token']))
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    regenerate_session_periodically();
}

function verify_csrf_token()
{

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))
    {
        // Jika token tidak cocok, hentikan eksekusi dan berikan pesan error.
        set_flash_message('danger', 'Sesi tidak valid atau telah kedaluwarsa. Silakan coba lagi.');
        // Redirect kembali ke halaman sebelumnya jika memungkinkan, atau ke dashboard
        $redirect_url = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        header('Location: ' . $redirect_url);
        exit();
    }
}

/**
 * Me-regenerasi ID sesi secara berkala untuk mencegah session fixation.
 */
function regenerate_session_periodically()
{

    if (!isset($_SESSION['last_regen']))
    {
        $_SESSION['last_regen'] = time();
    }

    $session_duration = 1800; // 30 menit

    if (time() - $_SESSION['last_regen'] > $session_duration)
    {
        session_regenerate_id(TRUE);
        $_SESSION['last_regen'] = time();
    }
}

function set_flash_message($type, $message)
{

    $_SESSION['flash_message'] = [ 'type' => $type, 'message' => $message ];
}

function get_flash_message()
{

    if (isset($_SESSION['flash_message']))
    {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);

        return $message;
    }
    return NULL;
}

function log_query($query, $error = NULL)
{

    if (ENVIRONMENT !== 'development')
    {
        return;
    }
    $log_path = ROOT_PATH . '/logs';
    if (!is_dir($log_path))
    {
        mkdir($log_path, 0777, TRUE);

    }

    $log_file  = $log_path . '/query_log_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');

    $log_message = "[$timestamp]\n";
    $log_message .= "QUERY: " . trim($query) . "\n";
    if ($error)
    {

        $log_message .= "ERROR: " . trim($error) . "\n";
    }
    $log_message .= "--------------------------------------------------\n\n";

    // Tulis ke file log
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

function e($str)
{

    if ($str === NULL)
    {
        return '';
    }
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

function has_permission($permission_name)
{

    // Beri akses penuh jika peran adalah Developer
    if (isset($_SESSION['nama_role']) && $_SESSION['nama_role'] === 'Developer')
    {
        return TRUE;
    }

    if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions']))
    {
        return in_array($permission_name, $_SESSION['permissions']);
    }
    return FALSE;
}

function validate_email($email)
{

    return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
}

function validate_url($url)
{

    return filter_var($url, FILTER_VALIDATE_URL) !== FALSE;
}

function sanitize_input($data)
{

    if (is_array($data))
    {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generate_random_string($length = 16)
{

    return bin2hex(random_bytes($length / 2));
}

function format_date($date, $format = 'd/m/Y H:i')
{

    if (!$date) return '';
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

function get_client_ip()
{

    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function is_ajax_request()
{

    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function redirect($url, $statusCode = 303)
{

    header('Location: ' . $url, TRUE, $statusCode);
    exit();
}

function array_get($array, $key, $default = NULL)
{

    return isset($array[$key]) ? $array[$key] : $default;
}

function encrypt_id($id)
{

    static $encryption = NULL;

    if ($encryption === NULL)
    {
        // Pastikan constants sudah terdefinisi
        if (!defined('ENCRYPTION_KEY'))
        {
            error_log("ERROR: ENCRYPTION_KEY constant not defined");
            return FALSE;
        }
        require_once APP_PATH . '/core/Encryption.php';
        $encryption = new Encryption(ENCRYPTION_KEY);
    }

    if (!is_numeric($id))
    {
        error_log("ERROR: encrypt_id requires numeric ID, got: " . gettype($id));
        return FALSE;
    }

    return $encryption->encryptConsistent((string) $id);
}

function decrypt_id($encrypted_id)
{

    static $encryption = NULL;

    if ($encryption === NULL)
    {
        // Pastikan constants sudah terdefinisi
        if (!defined('ENCRYPTION_KEY'))
        {
            error_log("ERROR: ENCRYPTION_KEY constant not defined");
            return FALSE;
        }
        require_once APP_PATH . '/core/Encryption.php';
        $encryption = new Encryption(ENCRYPTION_KEY);
    }

    $decrypted = $encryption->decryptConsistent($encrypted_id);
    return ($decrypted !== FALSE && is_numeric($decrypted)) ? (int) $decrypted : FALSE;
}

function validate_encrypted_id($encrypted_id)
{

    $decrypted = decrypt_id($encrypted_id);
    return $decrypted !== FALSE && is_numeric($decrypted);
}

function get_decrypted_id($encrypted_id, $default = NULL)
{

    $decrypted = decrypt_id($encrypted_id);
    return ($decrypted !== FALSE && is_numeric($decrypted)) ? (int) $decrypted : $default;
}