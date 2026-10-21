<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['p10_user_id']) || isset($_COOKIE['remember_me'])) {
    require_once __DIR__ . '/db_mysqli.php';
    require_once __DIR__ . '/remember_me.php';

    $uid = isset($_SESSION['p10_user_id']) ? (int) $_SESSION['p10_user_id'] : null;
    revoke_remember_me_cookie($mysqli, $uid);
    $mysqli->close();
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,   // Set expiry in the past
            'path'     => $params["path"],
            'domain'   => $params["domain"],
            'secure'   => $params["secure"],
            'httponly' => $params["httponly"],
            'samesite' => 'Lax',
        ]
    );
}

session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

header("Location: login.php?reason=logout");
exit;
?>
