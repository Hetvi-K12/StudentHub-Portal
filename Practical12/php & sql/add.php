<?php
$required_role = 'admin';
require_once __DIR__ . '/../MySQL3/auth.php';
require_once __DIR__ . '/db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $poster_path = null;

    if ($name === '' || $event_date === '') {
        $error = 'Event name and date are required.';
    } else {
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = $_FILES['poster'];
            if ($upload['error'] !== UPLOAD_ERR_OK) {
                $error = 'File upload error. Please try again.';
            } elseif ($upload['size'] > 2 * 1024 * 1024) {
                $error = 'Poster must be 2 MB or smaller.';
            } else {
                $finfo    = new finfo(FILEINFO_MIME_TYPE);
                $mime     = $finfo->file($upload['tmp_name']);
                $allowed  = ['image/jpeg', 'image/png', 'image/webp'];
                $ext_map  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (!in_array($mime, $allowed, true)) {
                    $error = 'Only JPEG, PNG, and WebP images are allowed.';
                } else {
                    $upload_dir = __DIR__ . '/posters/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $filename    = bin2hex(random_bytes(16)) . '.' . $ext_map[$mime];
                    $destination = $upload_dir . $filename;
                    if (move_uploaded_file($upload['tmp_name'], $destination)) {
                        $poster_path = 'posters/' . $filename;
                    } else {
                        $error = 'Failed to save the uploaded poster.';
                    }
                }
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare("INSERT INTO events (name, description, event_date, venue, poster_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $description, $event_date, $venue, $poster_path);
            $stmt->execute();
            header("Location: index.php?msg=" . urlencode("Event added successfully.") . "&type=ok");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Add Event</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="../MySQL3/admin_dashboard.php">Dashboard</a> &nbsp;
        <a href="index.php">Event List</a> &nbsp;
        <a href="../MySQL3/logout.php">Logout</a> &nbsp;
    </nav>

    <?php if ($error): ?>
    <div class="p12-msg p12-err"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="p12-form-wrap">
        <form method="POST" enctype="multipart/form-data">
            <label>Event Name <span class="p12-req">*</span></label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>

            <label>Event Date <span class="p12-req">*</span></label>
            <input type="date" name="event_date" id="event_date" value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>" required>

            <label>Venue</label>
            <input type="text" name="venue" id="venue" value="<?php echo htmlspecialchars($_POST['venue'] ?? ''); ?>">

            <label>Description</label>
            <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

            <label>Event Poster <small>(JPEG, PNG, WebP — max 2 MB)</small></label>
            <input type="file" name="poster" id="poster" accept=".jpg,.jpeg,.png,.webp">

            <button type="submit" class="p12-btn p12-btn-submit">Add Event</button>
            <a href="index.php" class="p12-btn p12-btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
