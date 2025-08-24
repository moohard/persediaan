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