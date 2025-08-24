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