<?php
$required_role = 'admin';
require_once __DIR__ . '/auth.php';

$admin_email   = htmlspecialchars($_SESSION['p10_email'] ?? 'Admin');
$admin_role    = htmlspecialchars($_SESSION['p10_role']  ?? 'admin');
$idle_since    = time() - ($_SESSION['p10_last_activity'] ?? time());
$mins_remaining = max(0, (int) ceil((900 - $idle_since) / 60));


// Load basic stats from the database for admin view
require_once __DIR__ . '/db_mysqli.php';
if (!isset($mysqli) || $mysqli === null) {
    die('<h2 style="color:#c00;">Database connection failed. Please start MySQL server.</h2>');
}
$total_students = 0;
$total_admins   = 0;
$recent_users   = [];

$count_result = $mysqli->query(
    "SELECT role, COUNT(*) AS cnt FROM students GROUP BY role"
);
if ($count_result) {
    while ($row = $count_result->fetch_assoc()) {
        if ($row['role'] === 'student') $total_students = (int) $row['cnt'];
        if ($row['role'] === 'admin')   $total_admins   = (int) $row['cnt'];
    }
}

// Fetch 5 most recently registered users (email + role, NO password data)
$recent_result = $mysqli->query(
    "SELECT id, email, role, last_login, created_at
       FROM students
      ORDER BY created_at DESC
      LIMIT 5"
);
if ($recent_result) {
    while ($row = $recent_result->fetch_assoc()) {
        $recent_users[] = $row;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Dashboard — StudentHub Portal Practical 10">
    <title>Admin Dashboard — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .p10-timeout-bar {
            background: #fff3cd;
            color: #856404;
            border-bottom: 2px solid #ffc107;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }
        .p10-session-info {
            max-width: 700px;
            margin: 0 auto 20px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 12px;
            padding: 14px 20px;
            color: #856404;
            font-size: 14px;
        }
        .p10-session-info code {
            background: #ffe08a;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .p10-stats {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 20px;
            flex-wrap: wrap;
        }
        .p10-stat-card {
            background: #fff;
            border-top: 4px solid #C89B3C;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
            padding: 24px 30px;
            text-align: center;
            min-width: 140px;
            transition: .3s;
        }
        .p10-stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,.15);
            border-top-color: #16233B;
        }
        .p10-stat-card .num { font-size: 36px; font-weight: bold; color: #16233B; }
        .p10-stat-card .lbl { font-size: 14px; color: #666; margin-top: 4px; }
        .admin-badge {
            display: inline-block;
            background: #C89B3C;
            color: #16233B;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .student-badge {
            display: inline-block;
            background: #16233B;
            color: #fff;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <?php if ($mins_remaining <= 3): ?>
    <div class="p10-timeout-bar" id="timeoutWarning">
        &#9888; Your session will expire in approximately <strong><?php echo $mins_remaining; ?> minute(s)</strong>.
    </div>
    <?php endif; ?>

    <h1>Admin Dashboard</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="login.php">Login</a> &nbsp;
        <a href="register.php">Register</a> &nbsp;
        <a href="admin_dashboard.php">Dashboard</a> &nbsp;
        <a href="../MySQL4/index.php">Student Management</a> &nbsp;
        <a href="logout.php">Logout</a> &nbsp;
    </nav>

    <div class="p10-session-info">
        &#128272; Admin session: <code><?php echo $admin_email; ?></code> &nbsp;|&nbsp;
        Role: <code><?php echo $admin_role; ?></code> &nbsp;|&nbsp;
        Session timeout in: <code><?php echo $mins_remaining; ?> min</code>
    </div>

    <div style="text-align:center; font-weight:bold; margin-bottom:10px; background:transparent; box-shadow:none;">
        Welcome, <?php echo $admin_email; ?>! You are logged in as an <strong>Administrator</strong>.
    </div>

    <!-- Summary Statistics -->
    <div class="p10-stats">
        <div class="p10-stat-card">
            <div class="num"><?php echo $total_students; ?></div>
            <div class="lbl">Students</div>
        </div>
        <div class="p10-stat-card">
            <div class="num"><?php echo $total_admins; ?></div>
            <div class="lbl">Admins</div>
        </div>
        <div class="p10-stat-card">
            <div class="num"><?php echo $total_students + $total_admins; ?></div>
            <div class="lbl">Total Users</div>
        </div>
    </div>

    <!-- Recent Users Table -->
    <h2>Recent Registrations (last 5)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Last Login</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($recent_users)): ?>
            <tr><td colspan="5">No users found.</td></tr>
        <?php else: ?>
            <?php foreach ($recent_users as $u): ?>
            <tr>
                <td><?php echo (int) $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="admin-badge">Admin</span>
                    <?php else: ?>
                        <span class="student-badge">Student</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $u['last_login'] ? htmlspecialchars($u['last_login']) : '—'; ?></td>
                <td><?php echo htmlspecialchars($u['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <script>
    (function () {
        var timeoutSeconds = 900;
        var secondsLeft = timeoutSeconds - <?php echo $idle_since; ?>;
        var bar = document.getElementById('timeoutWarning');
        if (secondsLeft <= 0) return;
        setInterval(function () {
            secondsLeft -= 30;
            if (secondsLeft <= 0) {
                window.location.href = 'login.php?reason=timeout';
            } else if (secondsLeft <= 180 && bar) {
                var minsLeft = Math.ceil(secondsLeft / 60);
                bar.style.display = 'block';
                bar.innerHTML = '&#9888; Session expires in <strong>' + minsLeft + ' minute(s)</strong>.';
            }
        }, 30000);
    })();
    </script>

</body>
</html>
