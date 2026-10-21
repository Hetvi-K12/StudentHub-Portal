<?php
require_once __DIR__ . '/db.php';

$errors = [];
$msg = '';
$name = $email = $mobile = $course = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $course = trim($_POST['course'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '') $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if ($mobile === '') $errors[] = 'Mobile is required.';
    elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) $errors[] = 'Enter a valid 10-digit mobile number.';
    if ($course === '') $errors[] = 'Course is required.';

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = 'A student with this email already exists.';
        } else {
            $hash = password_hash('password123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO students (name, email, mobile, course, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $mobile, $course, $hash);

            if ($stmt->execute()) {
                header("Location: index.php?msg=added");
                exit;
            } else {
                $errors[] = 'Failed to add student. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Practical 11</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="p11-header">
    <h1>➕ Add Student</h1>
</div>

<div class="p11-container">

    <div class="p11-nav">
        <a href="index.php">Student List</a>
        <a href="add.php">Add Student</a>
        <a href="../mysql/index.html">Home</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="p11-msg p11-msg-error">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="p11-form" method="POST" action="add.php">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="mobile">Mobile Number</label>
        <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" required>

        <label for="course">Course</label>
        <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($course); ?>" required>

        <button type="submit">Add Student</button>
    </form>

</div>

</body>
</html>
