<?php
require_once __DIR__ . '/../MySQL3/auth.php';
require_once __DIR__ . '/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$students = [];

if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT id, name, email, mobile, course, created_at FROM students WHERE name LIKE ? OR email LIKE ? OR course LIKE ? ORDER BY id ASC");
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT id, name, email, mobile, course, created_at FROM students ORDER BY id ASC");
}

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Practical 11</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="p11-header">
    <h1>Student Management</h1>
</div>

<div class="p11-container">

    <div class="p11-nav">
        <a href="index.php">Student List</a>
        <a href="add.php">Add Student</a>
        <a href="../mysql/index.html">Home</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="p11-msg <?php
            if ($_GET['msg'] === 'added') echo 'p11-msg-success';
            elseif ($_GET['msg'] === 'updated') echo 'p11-msg-success';
            elseif ($_GET['msg'] === 'deleted') echo 'p11-msg-success';
            else echo 'p11-msg-info';
        ?>">
            <?php
            if ($_GET['msg'] === 'added') echo '✅ Student added successfully!';
            elseif ($_GET['msg'] === 'updated') echo '✅ Student updated successfully!';
            elseif ($_GET['msg'] === 'deleted') echo '✅ Student deleted successfully!';
            ?>
        </div>
    <?php endif; ?>

    <div class="p11-search-box">
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search by name, email, or course..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" style="margin-left:10px; color:#16233B; font-weight:bold;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <p class="p11-count">Total records: <?php echo count($students); ?></p>

    <?php if ($search !== '' && count($students) === 0): ?>
        <div class="p11-msg p11-msg-info">No matching records found for "<strong><?php echo htmlspecialchars($search); ?></strong>".</div>
    <?php elseif (count($students) > 0): ?>
        <table class="p11-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $i => $s): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($s['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['mobile']); ?></td>
                    <td><?php echo htmlspecialchars($s['course'] ?? ''); ?></td>
                    <td class="p11-actions">
                        <a href="edit.php?id=<?php echo $s['id']; ?>" class="p11-btn-edit">Edit</a>
                        <a href="delete.php?id=<?php echo $s['id']; ?>" class="p11-btn-delete">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="p11-msg p11-msg-info">No student records found. <a href="add.php">Add one</a>.</div>
    <?php endif; ?>

</div>

</body>
</html>
