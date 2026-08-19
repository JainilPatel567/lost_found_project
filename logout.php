<?php

require_once 'includes/auth.php';
require_once 'includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,      // Set to past time to expire
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();



header("Location: login.php?msg=You+have+been+logged+out+successfully.");
exit();
?>
