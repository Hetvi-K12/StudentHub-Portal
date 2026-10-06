<?php
$message = '';
$status = '';
$email = '';
$mobile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($email)) {
        $status = 'error';
        $message = 'Registration Failed! Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = 'error';
        $message = 'Registration Failed! Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $status = 'error';
        $message = 'Registration Failed! Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $status = 'error';
        $message = 'Password and Confirm Password do not match.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $timestamp = date('Y-m-d H:i:s');

        $csv_file = __DIR__ . '/registrations.csv';
        $file_exists = file_exists($csv_file);
        $is_empty = !$file_exists || filesize($csv_file) === 0;

        $fp = fopen($csv_file, 'a');
        if ($fp !== false) {
            if ($is_empty) {
                fputcsv($fp, ['Email', 'Mobile', 'Password', 'Timestamp']);
            }
            fputcsv($fp, [$email, $mobile, $hashed_password, $timestamp]);
            fclose($fp);

            $status = 'success';
            $message = 'Registration successful!';
            $email = '';
            $mobile = '';
        } else {
            $status = 'error';
            $message = 'Unable to write to registrations.csv file.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="register">
    <h1>Welcome!</h1>
    <nav>
        <a href = "index.html">Home</a> &nbsp;   
    </nav>
    <br>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo $status; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST" novalidate>

        <label>Email ID:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label>Mobile Number:</label>
        <input type="tel" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" required>

        <label>Password:</label>
        <input type="password" id="password" name="password" required>

        <label>Confirm Password:</label>
        <input type="password" id="confirm-password" name="confirm_password" required>

        <button type="submit">Sign Up</button>

    </form>

    <p>Already have an account?</p>
    <a href="login.html">Login</a>
    <script src="script.js"></script>
</body>
</html>
