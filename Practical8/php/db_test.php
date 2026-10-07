<?php
// =======================================================================
// STUDENTHUB PORTAL - DATABASE CONNECTION TEST (db_test.php)
// =======================================================================
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>StudentHub Portal</h1>
        <h2>Database Connection Test</h2>
        <nav>
            <a href="index.html">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <br>
    <?php if (isset($pdo) && $pdo instanceof PDO): ?>
        <div class="php-message php-success" style="font-size: 20px; text-align: center; margin: 40px auto;">
            Database connection successful!
        </div>
    <?php else: ?>
        <div class="php-message php-error" style="font-size: 20px; text-align: center; margin: 40px auto;">
            Database connection failed.
        </div>
    <?php endif; ?>
</body>
</html>
