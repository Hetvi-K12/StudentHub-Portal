<?php

require_once __DIR__ . '/db_mysqli.php';

$message       = '';
$status        = '';
$email         = '';
$mobile        = '';
$selected_role = 'student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email         = isset($_POST['email'])            ? trim($_POST['email'])   : '';
    $mobile        = isset($_POST['mobile'])           ? trim($_POST['mobile'])  : '';
    $password      = isset($_POST['password'])         ? $_POST['password']      : '';
    $confirm_pass  = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $role          = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'student';
    $selected_role = $role;

    // ── Validation ────────────────────────────────────────────────────
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status  = 'error';
        $message = 'Please enter a valid email address.';

    } elseif (empty($mobile) || !preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
        $status  = 'error';
        $message = 'Mobile number must be 10 digits and start with 6, 7, 8, or 9.';

    } elseif (strlen($password) < 8) {
        $status  = 'error';
        $message = 'Password must be at least 8 characters long.';

    } elseif ($password !== $confirm_pass) {
        $status  = 'error';
        $message = 'Passwords do not match.';

    } else {

        // ── Check duplicate email ──────────────────────────────────────
        $chk = $mysqli->prepare("SELECT id FROM students WHERE email = ?");
        $chk->bind_param('s', $email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $status  = 'error';
            $message = 'This email is already registered. Please log in.';
        } else {

            // ── Check duplicate mobile ─────────────────────────────────
            $chk2 = $mysqli->prepare("SELECT id FROM students WHERE mobile = ?");
            $chk2->bind_param('s', $mobile);
            $chk2->execute();
            $chk2->store_result();

            if ($chk2->num_rows > 0) {
                $status  = 'error';
                $message = 'This mobile number is already registered.';
            } else {

                // ── Hash password and insert ───────────────────────────
                $hash   = password_hash($password, PASSWORD_DEFAULT);
                $insert = $mysqli->prepare(
                    "INSERT INTO students (email, mobile, password_hash, role) VALUES (?, ?, ?, ?)"
                );
                $insert->bind_param('ssss', $email, $mobile, $hash, $role);

                if ($insert->execute()) {
                    $status  = 'success';
                    $message = 'Registration successful! You can now log in as ' . ucfirst($role) . '.';
                    $email   = '';
                    $mobile  = '';
                } else {
                    $status  = 'error';
                    $message = 'Registration failed. Please try again.';
                }
                $insert->close();
            }
            $chk2->close();
        }
        $chk->close();
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StudentHub (Practical 10)</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Fix shared CSS label { display:block } for inline elements */
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
    </style>
</head>
<body>

    <h2>Create Account</h2>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="login.php">Login</a> &nbsp;
        <a href="register.php">Register</a> &nbsp;
    </nav>
    <br>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo $status; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($status === 'success'): ?>
        <p style="text-align:center;">
            <a href="login.php">Click here to Login</a>
        </p>
    <?php else: ?>

    <form action="register.php" method="POST" id="p10RegisterForm" novalidate>

        <!-- Register As: Student / Admin -->
        <div class="role-row">
            <span>Register As:</span>
            <label>
                <input type="radio" name="role" value="student"
                    <?php echo $selected_role === 'student' ? 'checked' : ''; ?>>
                Student
            </label>
            <label>
                <input type="radio" name="role" value="admin"
                    <?php echo $selected_role === 'admin' ? 'checked' : ''; ?>>
                Admin
            </label>
        </div>

        <label for="reg-email">Email Address:</label>
        <input type="email" id="reg-email" name="email"
               value="<?php echo htmlspecialchars($email); ?>"
               placeholder="Enter your email" required autocomplete="email">

        <label for="reg-mobile">Mobile Number:</label>
        <input type="tel" id="reg-mobile" name="mobile"
               value="<?php echo htmlspecialchars($mobile); ?>"
               placeholder="10-digit mobile number" required maxlength="10">

        <label for="reg-password">Password:</label>
        <input type="password" id="reg-password" name="password"
               placeholder="Minimum 8 characters" required autocomplete="new-password">

        <label for="reg-confirm">Confirm Password:</label>
        <input type="password" id="reg-confirm" name="confirm_password"
               placeholder="Re-enter your password" required autocomplete="new-password">

        <button type="submit">Register</button>

    </form>

    <p>Already have an account?</p>
    <a href="login.php">Login here</a>

    <?php endif; ?>

    <script>
    document.getElementById('p10RegisterForm') &&
    document.getElementById('p10RegisterForm').addEventListener('submit', function(e) {
        var email   = document.getElementById('reg-email').value.trim();
        var mobile  = document.getElementById('reg-mobile').value.trim();
        var pass    = document.getElementById('reg-password').value;
        var confirm = document.getElementById('reg-confirm').value;

        if (!email || !mobile || !pass || !confirm) {
            e.preventDefault(); alert('All fields are required.'); return;
        }
        if (pass.length < 8) {
            e.preventDefault(); alert('Password must be at least 8 characters.'); return;
        }
        if (pass !== confirm) {
            e.preventDefault(); alert('Passwords do not match.'); return;
        }
        if (!/^[6-9][0-9]{9}$/.test(mobile)) {
            e.preventDefault(); alert('Mobile must be 10 digits starting with 6, 7, 8, or 9.'); return;
        }
    });
    </script>

</body>
</html>
