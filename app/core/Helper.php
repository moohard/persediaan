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
    // ... (sisa kode log_query tidak berubah)
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