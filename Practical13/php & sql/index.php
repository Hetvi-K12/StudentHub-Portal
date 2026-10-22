<?php
$required_role = null;
require_once __DIR__ . '/../MySQL3/auth.php';
$is_admin   = ($_SESSION['p10_role'] ?? '') === 'admin';
$user_email = htmlspecialchars($_SESSION['p10_email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="My Courses — StudentHub Portal Practical 13">
    <title>My Courses — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <h1>My Courses</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <?php if ($is_admin): ?>
        <a href="../MySQL3/admin_dashboard.php">Dashboard</a> &nbsp;
        <?php endif; ?>
        <a href="../MySQL3/logout.php">Logout</a> &nbsp;
    </nav>

    <?php if ($is_admin): ?>
    <div class="p13-form-section">
        <h2 id="formTitle">Add New Course</h2>
        <form id="courseForm">
            <input type="hidden" id="editId" value="">

            <label for="courseName">Course Name</label>
            <input type="text" id="courseName" placeholder="e.g. Web Development" required>

            <label for="courseDesc">Description</label>
            <textarea id="courseDesc" rows="3" placeholder="Enter course description"></textarea>

            <label for="courseIcon">Font Awesome Icon Class</label>
            <input type="text" id="courseIcon" value="fas fa-book" placeholder="fas fa-book">

            <div class="p13-form-btns">
                <button type="submit" id="submitBtn" class="p13-btn p13-btn-save">Save Course</button>
                <button type="button" id="cancelBtn" class="p13-btn p13-btn-cancel" style="display:none;">Cancel</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div id="statusMsg" class="p13-status" style="display:none;"></div>

    <div id="courseGrid" class="p13-grid">
        <p id="loadingMsg">Loading courses...</p>
    </div>

    <script>
        const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;
        const API_URL  = 'api.php';
    </script>
    <script src="script.js"></script>
</body>
</html>
