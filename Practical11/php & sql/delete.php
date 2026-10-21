<?php
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, course FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $del = $conn->prepare("DELETE FROM students WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    header("Location: index.php?msg=deleted");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student - Practical 11</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="p11-header">
    <h1>🗑️ Delete Student</h1>
</div>

<div class="p11-container">

    <div class="p11-nav">
        <a href="index.php">Student List</a>
        <a href="add.php">Add Student</a>
        <a href="../mysql/index.html">Home</a>
    </div>

    <div class="p11-confirm-box">
        <h2>⚠️ Confirm Deletion</h2>
        <p>Are you sure you want to delete this student?</p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name'] ?? ''); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course'] ?? ''); ?></p>

        <form method="POST" action="delete.php?id=<?php echo $id; ?>" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit" class="p11-btn-delete">Yes, Delete</button>
        </form>
        <a href="index.php" class="p11-btn-cancel">Cancel</a>
    </div>

</div>

</body>
</html>
