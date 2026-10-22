<?php
$required_role = 'admin';
require_once __DIR__ . '/../MySQL3/auth.php';
require_once __DIR__ . '/db.php';

$admin_id    = (int)($_SESSION['p10_user_id'] ?? 0);
$admin_email = htmlspecialchars($_SESSION['p10_email'] ?? '');
$msg         = '';
$msg_type    = '';

// Handle role update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $target_id = (int)($_POST['user_id'] ?? 0);
    $new_role  = $_POST['role'] ?? '';

    if ($target_id > 0 && in_array($new_role, ['student', 'admin'], true) && $target_id !== $admin_id) {
        $old_stmt = $conn->prepare("SELECT email, role FROM students WHERE id = ?");
        $old_stmt->bind_param("i", $target_id);
        $old_stmt->execute();
        $old_row = $old_stmt->get_result()->fetch_assoc();

        if ($old_row) {
            $old_role     = $old_row['role'];
            $target_email = $old_row['email'];

            $upd = $conn->prepare("UPDATE students SET role = ? WHERE id = ?");
            $upd->bind_param("si", $new_role, $target_id);
            $upd->execute();

            $details = "Admin {$admin_email} changed user ID {$target_id} ({$target_email}) role from {$old_role} to {$new_role}.";
            $log = $conn->prepare("INSERT INTO audit_logs (admin_id, action, details) VALUES (?, 'STUDENT_UPDATED', ?)");
            $log->bind_param("is", $admin_id, $details);
            $log->execute();

            $msg      = "Role updated for {$target_email}.";
            $msg_type = 'ok';
        } else {
            $msg      = "User not found.";
            $msg_type = 'err';
        }
    } else {
        $msg      = "Invalid request or you cannot change your own role.";
        $msg_type = 'err';
    }
}

// Stats
$total_users    = (int)$conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$total_students = (int)$conn->query("SELECT COUNT(*) FROM students WHERE role = 'student'")->fetch_row()[0];
$total_events   = (int)$conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0];

// Latest registrations (most recently registered users)
$reg_result    = $conn->query("SELECT id, email, role, created_at FROM students ORDER BY created_at DESC LIMIT 10");
$registrations = [];
while ($row = $reg_result->fetch_assoc()) $registrations[] = $row;

// All users for role management
$users_result = $conn->query("SELECT id, email, role FROM students ORDER BY created_at DESC");
$all_users = [];
while ($row = $users_result->fetch_assoc()) $all_users[] = $row;

// Audit log
try {
    $log_result = $conn->query(
        "SELECT al.id, s.email AS admin_email, al.action, al.details, al.created_at
           FROM audit_logs al
           LEFT JOIN students s ON al.admin_id = s.id
          ORDER BY al.created_at DESC
          LIMIT 10"
    );
    $audit_logs = [];
    while ($row = $log_result->fetch_assoc()) $audit_logs[] = $row;
} catch (Exception $e) {
    $audit_logs = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Dashboard — StudentHub Portal Practical 14">
    <title>Admin Dashboard — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Admin Dashboard</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="../MySQL3/admin_dashboard.php">P10 Dashboard</a> &nbsp;
        <a href="../MySQL4/index.php">Student Management</a> &nbsp;
        <a href="../MySQL5/index.php">Event Management</a> &nbsp;
        <a href="../MySQL6/index.php">My Courses</a> &nbsp;
        <a href="index.php">P14 Dashboard</a> &nbsp;
        <a href="../MySQL3/logout.php">Logout</a>
    </nav>

    <?php if ($msg): ?>
    <div class="p14-msg p14-msg-<?php echo $msg_type; ?>"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <p class="p14-welcome">Welcome, <strong><?php echo $admin_email; ?></strong>. You are logged in as <strong>Admin</strong>.</p>

    <!-- Stats -->
    <div class="p14-stats">
        <div class="p14-stat-card">
            <div class="p14-stat-icon">&#128101;</div>
            <div class="p14-stat-num"><?php echo $total_users; ?></div>
            <div class="p14-stat-lbl">Total Users</div>
        </div>
        <div class="p14-stat-card">
            <div class="p14-stat-icon">&#127891;</div>
            <div class="p14-stat-num"><?php echo $total_students; ?></div>
            <div class="p14-stat-lbl">Students</div>
        </div>
        <div class="p14-stat-card">
            <div class="p14-stat-icon">&#128197;</div>
            <div class="p14-stat-num"><?php echo $total_events; ?></div>
            <div class="p14-stat-lbl">Events</div>
        </div>
    </div>

    <!-- User Role Management -->
    <div class="p14-section">
        <h2>User Management <small>(Role-Based Access Control)</small></h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($all_users as $u): ?>
                <tr>
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <?php if ((int)$u['id'] === $admin_id): ?>
                            <span class="p14-badge p14-badge-admin"><?php echo htmlspecialchars($u['role']); ?></span>
                        <?php else: ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                <select name="role" class="p14-select">
                                    <option value="student" <?php echo $u['role']==='student'?'selected':''; ?>>Student</option>
                                    <option value="admin"   <?php echo $u['role']==='admin'  ?'selected':''; ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="p14-btn p14-btn-update">Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo (int)$u['id'] === $admin_id ? '<span class="p14-self">Current account</span>' : ''; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Latest Registrations -->
    <div class="p14-section">
        <h2>Latest Registrations</h2>
        <?php if (empty($registrations)): ?>
            <p class="p14-empty">No registrations found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($registrations as $r): ?>
                <tr>
                    <td><?php echo (int)$r['id']; ?></td>
                    <td><?php echo htmlspecialchars($r['email']); ?></td>
                    <td><span class="p14-badge p14-badge-<?php echo $r['role']==='admin'?'admin':'student'; ?>"><?php echo htmlspecialchars(ucfirst($r['role'])); ?></span></td>
                    <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Audit Log -->
    <div class="p14-section">
        <h2>Audit Log &mdash; Recent Admin Actions</h2>
        <?php if (empty($audit_logs)): ?>
            <p class="p14-empty">No audit log entries yet.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Date and Time</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($audit_logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['admin_email'] ?? 'Unknown'); ?></td>
                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                    <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</body>
</html>
