<?php

function set_remember_me_cookie(mysqli $mysqli, int $user_id): void
{
    $raw_token = bin2hex(random_bytes(32));

    $token_hash = hash('sha256', $raw_token);

    $expiry_timestamp = time() + REMEMBER_ME_LIFETIME;
    $expiry_datetime  = date('Y-m-d H:i:s', $expiry_timestamp);

    $stmt = $mysqli->prepare(
        "UPDATE students
            SET remember_token = ?, token_expiry = ?
          WHERE id = ?"
    );
    $stmt->bind_param("ssi", $token_hash, $expiry_datetime, $user_id);
    $stmt->execute();
    $stmt->close();

    setcookie(
        'remember_me',          
        $raw_token,             
        [
            'expires'  => $expiry_timestamp,
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,   
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

function restore_session_from_cookie(?mysqli $mysqli_param): void
{
    if ($mysqli_param === null) {
        require_once __DIR__ . '/db_mysqli.php';
        $conn = $mysqli;
    } else {
        $conn = $mysqli_param;
    }

    $raw_token = $_COOKIE['remember_me'] ?? '';

    if (strlen($raw_token) !== 64 || !ctype_xdigit($raw_token)) {
        revoke_remember_me_cookie($conn);
        return;
    }

    $token_hash = hash('sha256', $raw_token);

    $stmt = $conn->prepare(
        "SELECT id, email, role, token_expiry
           FROM students
          WHERE remember_token = ?
          LIMIT 1"
    );
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        revoke_remember_me_cookie($conn);
        return;
    }

    if ($user['token_expiry'] === null || strtotime($user['token_expiry']) < time()) {
        revoke_remember_me_cookie($conn);
        return;
    }

    session_regenerate_id(true);

    $_SESSION['p10_user_id']       = (int) $user['id'];
    $_SESSION['p10_email']         = $user['email'];
    $_SESSION['p10_role']          = $user['role'];
    $_SESSION['p10_last_activity'] = time();

    set_remember_me_cookie($conn, (int) $user['id']);

    $update = $conn->prepare(
        "UPDATE students SET last_login = NOW() WHERE id = ?"
    );
    $update->bind_param("i", $user['id']);
    $update->execute();
    $update->close();
}

function revoke_remember_me_cookie(?mysqli $conn, ?int $user_id = null): void
{
    setcookie('remember_me', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'domain'   => '',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    if ($conn === null) {
        return;
    }

    if ($user_id !== null) {
        $stmt = $conn->prepare(
            "UPDATE students
                SET remember_token = NULL, token_expiry = NULL
              WHERE id = ?"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $raw_token = $_COOKIE['remember_me'] ?? '';
        if (strlen($raw_token) === 64 && ctype_xdigit($raw_token)) {
            $token_hash = hash('sha256', $raw_token);
            $stmt = $conn->prepare(
                "UPDATE students
                    SET remember_token = NULL, token_expiry = NULL
                  WHERE remember_token = ?"
            );
            $stmt->bind_param("s", $token_hash);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
