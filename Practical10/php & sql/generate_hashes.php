<?php
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    http_response_code(403);
    die("This file is for local development only.");
}

$student_pass = 'Student@123';
$admin_pass   = 'Admin@123';

$options = ['cost' => 12];

$student_hash = password_hash($student_pass, PASSWORD_BCRYPT, $options);
$admin_hash   = password_hash($admin_pass,   PASSWORD_BCRYPT, $options);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hash Generator — Practical 10</title>
    <style>
        body { font-family: monospace; background: #f5f7fb; padding: 30px; }
        pre  { background: #fff; padding: 20px; border-left: 4px solid #C89B3C; border-radius: 8px; }
    </style>
</head>
<body>
<h2>Practical 10 — Password Hash Generator</h2>
<p><strong>student10@test.com</strong> / Student@123</p>
<pre><?php echo htmlspecialchars($student_hash); ?></pre>

<p><strong>admin10@test.com</strong> / Admin@123</p>
<pre><?php echo htmlspecialchars($admin_hash); ?></pre>

<p>Verification checks:</p>
<pre>
Student hash valid: <?php echo password_verify($student_pass, $student_hash) ? 'YES ✓' : 'NO ✗'; ?>

Admin hash valid:   <?php echo password_verify($admin_pass, $admin_hash)   ? 'YES ✓' : 'NO ✗'; ?>

</pre>
<hr>
<p style="color:red;">⚠️ Delete or restrict this file after use. Do NOT commit it with real credentials.</p>
</body>
</html>
