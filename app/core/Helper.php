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

/**
 * --- FITUR BARU: QUERY LOGGING ---
 * Fungsi untuk mencatat query SQL ke dalam file log.
 * Hanya aktif jika ENVIRONMENT diatur ke 'development'.
 */
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