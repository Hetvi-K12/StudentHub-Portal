<?php
// =======================================================================
// STUDENTHUB PORTAL - PRACTICAL 8 STUDENT REGISTRATION (register.php)
// =======================================================================

require_once __DIR__ . '/db.php';

$message = '';
$status = '';
$email = '';
$mobile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // 1. Email validation
    if (empty($email)) {
        $status = 'error';
        $message = 'Registration Failed! Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = 'error';
        $message = 'Registration Failed! Please enter a valid email address.';
    } 
    // 2. Mobile validation (Server-side rule: ^[6-9][0-9]{9}$)
    elseif (empty($mobile) || !preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
        $status = 'error';
        $message = 'Registration Failed! Mobile number must be 10 digits and start with 6, 7, 8, or 9.';
    } 
    // 3. Password validation (At least 8 characters)
    elseif (strlen($password) < 8) {
        $status = 'error';
        $message = 'Registration Failed! Password must be at least 8 characters long.';
    } 
    // 4. Confirm password validation
    elseif ($password !== $confirm_password) {
        $status = 'error';
        $message = 'Password and Confirm Password do not match.';
    } 
    else {
        try {
            // 5. Check duplicate email in MySQL using PDO Prepared Statement
            $checkStmt = $pdo->prepare("SELECT id FROM students WHERE email = :email");
            $checkStmt->execute([':email' => $email]);

            if ($checkStmt->fetch()) {
                $status = 'error';
                $message = 'Registration Failed! This email is already registered.';
            } else {
                // 6. Hash password using password_hash()
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 7. Prepared INSERT statement
                $insertStmt = $pdo->prepare(
                    "INSERT INTO students (email, mobile, password_hash) VALUES (:email, :mobile, :password_hash)"
                );

                $insertStmt->execute([
                    ':email' => $email,
                    ':mobile' => $mobile,
                    ':password_hash' => $hashed_password
                ]);

                $status = 'success';
                $message = 'Registration successful!';
                $email = '';
                $mobile = '';
            }
        } catch (PDOException $e) {
            $status = 'error';
            $message = 'Database error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="register">
    <h1>Welcome!</h1>
    <nav>
        <a href="index.html">Home</a> &nbsp;   
        <a href="login.php">Login</a> &nbsp;
        <a href="register.php">Register</a> &nbsp;
    </nav>
    <br>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo htmlspecialchars($status); ?>">
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
    <a href="login.php">Login</a>
    <script src="script.js"></script>
</body>
</html>
