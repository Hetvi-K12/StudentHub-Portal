<?php
$required_role = 'admin';
require_once __DIR__ . '/../MySQL3/auth.php';
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, name, poster_path FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    header("Location: index.php?msg=" . urlencode("Event not found.") . "&type=err");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $del_stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $del_stmt->bind_param("i", $id);
    $del_stmt->execute();

    if ($event['poster_path']) {
        $poster_file = __DIR__ . '/' . $event['poster_path'];
        if (file_exists($poster_file) && strpos(realpath($poster_file), realpath(__DIR__ . '/posters/')) === 0) {
            unlink($poster_file);
        }
    }

    header("Location: index.php?msg=" . urlencode("Event deleted successfully.") . "&type=ok");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Delete Event</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="../MySQL3/admin_dashboard.php">Dashboard</a> &nbsp;
        <a href="index.php">Event List</a> &nbsp;
        <a href="../MySQL3/logout.php">Logout</a> &nbsp;
    </nav>

    <div class="p12-form-wrap p12-confirm-box">
        <p>Are you sure you want to delete the event:</p>
        <h2><?php echo htmlspecialchars($event['name']); ?></h2>
        <p>This action cannot be undone.</p>
        <form method="POST">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="p12-btn p12-btn-delete">Yes, Delete</button>
            <a href="index.php" class="p12-btn p12-btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
