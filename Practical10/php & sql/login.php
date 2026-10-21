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

if (isset($_SESSION['p10_user_id'])) {
    $role = $_SESSION['p10_role'] ?? 'student';
    header('Location: ' . ($role === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'));
    exit;
}

require_once __DIR__ . '/db_mysqli.php';
require_once __DIR__ . '/remember_me.php';

$message       = '';
$status        = '';
$email_display = '';
$selected_role = 'student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email         = isset($_POST['email'])      ? trim($_POST['email']) : '';
    $password      = isset($_POST['password'])   ? $_POST['password']    : '';
    $login_role    = isset($_POST['login_role']) ? trim($_POST['login_role']) : 'student';
    $remember_me   = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

    $selected_role = in_array($login_role, ['student', 'admin'], true) ? $login_role : 'student';
    $email_display = $email;

    if (empty($email) || empty($password)) {
        $status  = 'error';
        $message = 'Please enter both email and password.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status  = 'error';
        $message = 'Please enter a valid email address.';

    } else {

        try {
            // Fetch user by email — prepared statement prevents SQL injection
            $stmt = $mysqli->prepare(
                "SELECT id, email, password_hash, role FROM students WHERE email = ? LIMIT 1"
            );
            if (!$stmt) {
                throw new Exception("prepare_failed");
            }
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Dummy hash prevents timing attacks when user not found
            $dummy   = '$2y$12$invalidhashXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.XXXXXXXX';
            $hash    = $user ? $user['password_hash'] : $dummy;
            $pass_ok = password_verify($password, $hash);

            // Check password AND that the DB role matches the selected role
            $role_ok = $user && ($user['role'] === $selected_role);

            if (!$user || !$pass_ok || !$role_ok) {
                $status  = 'error';
                $message = 'Invalid email or password. Please try again.';

            } else {
                // SUCCESS — regenerate session ID (prevents session fixation)
                session_regenerate_id(true);

                $_SESSION['p10_user_id']       = (int) $user['id'];
                $_SESSION['p10_email']         = $user['email'];
                $_SESSION['p10_role']          = $user['role'];
                $_SESSION['p10_last_activity'] = time();

                // Update last_login timestamp
                $ts = $mysqli->prepare("UPDATE students SET last_login = NOW() WHERE id = ?");
                $ts->bind_param('i', $user['id']);
                $ts->execute();
                $ts->close();

                // Set remember-me cookie if checked
                if ($remember_me) {
                    set_remember_me_cookie($mysqli, (int) $user['id']);
                }

                $mysqli->close();

                // Redirect based on role
                header('Location: ' . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'));
                exit;
            }

        } catch (Exception $e) {
            // Likely cause: migration has not been run yet (role column missing)
            $status  = 'error';
            $message = 'Database setup incomplete. Please run MySQL3/sql/p10_migration.sql '
                     . 'in phpMyAdmin first, then try again.';
        }
    }
}

if (isset($mysqli) && $mysqli instanceof mysqli) {
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StudentHub (Practical 10)</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Fix: shared CSS sets label { display:block } which breaks inline elements */
        .role-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .role-row span {
            font-size: 17px;
            font-weight: bold;
            color: #16233B;
        }
        .role-row label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 16px;
            font-weight: bold;
            color: #16233B;
            margin: 0;
            cursor: pointer;
        }
        .role-row input[type="radio"] {
            width: auto;
            margin: 0;
            padding: 0;
            accent-color: #C89B3C;
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .remember-row input[type="checkbox"] {
            width: auto;
            margin: 0;
            padding: 0;
            accent-color: #C89B3C;
        }
        .remember-row label {
            display: inline;
            font-size: 15px;
            font-weight: normal;
            color: #16233B;
            margin: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h2>Welcome Back!</h2>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="login.php">Login</a> &nbsp;
        <a href="register.php">Register</a> &nbsp;
    </nav>
    <br>

    <?php if (isset($_GET['reason']) && $_GET['reason'] === 'timeout'): ?>
        <div class="php-message php-error">
            Your session expired after 15 minutes of inactivity. Please log in again.
        </div>
    <?php elseif (isset($_GET['reason']) && $_GET['reason'] === 'logout'): ?>
        <div class="php-message php-success">
            You have been logged out successfully.
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo htmlspecialchars($status); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form id="p10LoginForm" action="login.php" method="POST" novalidate>

        <!-- Role selector: inline radio buttons -->
        <div class="role-row">
            <span>Login As:</span>
            <label>
                <input type="radio" name="login_role" value="student"
                    <?php echo $selected_role === 'student' ? 'checked' : ''; ?>>
                Student
            </label>
            <label>
                <input type="radio" name="login_role" value="admin"
                    <?php echo $selected_role === 'admin' ? 'checked' : ''; ?>>
                Admin
            </label>
        </div>

        <label for="p10-email">Email Address:</label>
        <input type="email" id="p10-email" name="email"
               value="<?php echo htmlspecialchars($email_display); ?>"
               placeholder="Enter your email" required autocomplete="email">

        <label for="p10-password">Password:</label>
        <input type="password" id="p10-password" name="password"
               placeholder="Enter your password" required autocomplete="current-password">

        <!-- Remember Me: inline checkbox + label -->
        <div class="remember-row">
            <input type="checkbox" id="p10-remember" name="remember_me" value="1">
            <label for="p10-remember">Remember me for 30 days</label>
        </div>

        <button type="submit">Login</button>

    </form>

    <p>Don't have an account?</p>
    <a href="../MySQL2/register.php">Register here</a>

    <script>
    document.getElementById('p10LoginForm').addEventListener('submit', function(e) {
        var email    = document.getElementById('p10-email').value.trim();
        var password = document.getElementById('p10-password').value;
        if (!email || !password) {
            e.preventDefault();
            alert('Please enter both email and password.');
        }
    });
    </script>

</body>
</html>
