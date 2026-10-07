<?php
// =======================================================================
// STUDENTHUB PORTAL - PRACTICAL 8 STUDENT LOGIN (login.php)
// =======================================================================

session_start();
require_once __DIR__ . '/db.php';

$message = '';
$status = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['username']) ? trim($_POST['username']) : (isset($_POST['email']) ? trim($_POST['email']) : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $status = 'error';
        $message = 'Please enter both email and password.';
    } else {
        try {
            // Prepared statement to search student by email
            $stmt = $pdo->prepare("SELECT id, email, password_hash FROM students WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $student = $stmt->fetch();

            if (!$student) {
                // Unregistered user login failure
                $status = 'error';
                $message = 'Login Failed! Please register first.';
            } else {
                // Verify hashed password
                if (password_verify($password, $student['password_hash'])) {
                    // Successful login -> Create PHP session
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_email'] = $student['email'];

                    header("Location: dashboard.php");
                    exit;
                } else {
                    // Wrong password
                    $status = 'error';
                    $message = 'Incorrect email or password.';
                }
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
    <title>Login - StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Welcome Back!</h2>

    <nav>
        <a href="index.html">Home</a> &nbsp;
    </nav>
    <br>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo htmlspecialchars($status); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST" novalidate>

        <label>Username / Email:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($email); ?>" required>

        <label>Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account?</p>
    <a href="register.php">Sign Up</a>
    <script src="script.js"></script>
</body>
</html>
