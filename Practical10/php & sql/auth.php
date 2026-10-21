<?php
if (!isset($required_role)) {
    $required_role = null;
}

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

define('SESSION_TIMEOUT_SECONDS', 900); 

if (isset($_SESSION['p10_user_id'])) {
    
    $last_active = $_SESSION['p10_last_activity'] ?? 0;
    $idle_seconds = time() - $last_active;

    if ($idle_seconds > SESSION_TIMEOUT_SECONDS) {

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $cookie_params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookie_params["path"],
                $cookie_params["domain"],
                $cookie_params["secure"],
                $cookie_params["httponly"]
            );
        }

        session_destroy();

        header("Location: login.php?reason=timeout");
        exit;
    }

    $_SESSION['p10_last_activity'] = time();
}

if (!isset($_SESSION['p10_user_id']) && isset($_COOKIE['remember_me'])) {
    require_once __DIR__ . '/remember_me.php';
    restore_session_from_cookie($mysqli ?? null);
}

if (!isset($_SESSION['p10_user_id'])) {
    header("Location: login.php");
    exit;
}

if ($required_role !== null) {
    $session_role = $_SESSION['p10_role'] ?? '';

    if ($session_role !== $required_role) {

        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Denied — StudentHub</title>
            <link rel="stylesheet" href="../css/style.css">
            <style>
                .denied-box {
                    max-width: 480px;
                    margin: 60px auto;
                    background: #fff;
                    border-radius: 18px;
                    box-shadow: 0 10px 30px rgba(0,0,0,.1);
                    padding: 40px;
                    text-align: center;
                    border-top: 4px solid #C89B3C;
                }
                .denied-box h2 { color: #16233B; margin-bottom: 16px; }
                .denied-box p  { color: #555; margin-bottom: 24px; }
                .denied-box a  {
                    display: inline-block;
                    background: #16233B;
                    color: #fff;
                    padding: 12px 28px;
                    border-radius: 30px;
                    font-weight: bold;
                    text-decoration: none;
                    transition: .3s;
                }
                .denied-box a:hover { background: #C89B3C; color: #16233B; }
            </style>
        </head>
        <body>
        <div class="denied-box">
            <h2>&#128274; Access Denied</h2>
            <p>You do not have permission to view this page.<br>
               Your role is <strong><?php echo htmlspecialchars($session_role); ?></strong>,
               but this page requires <strong><?php echo htmlspecialchars($required_role); ?></strong> access.</p>
            <a href="login.php">Go to Login</a>
        </div>
        </body>
        </html>
        <?php
        exit;
    }
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

?>