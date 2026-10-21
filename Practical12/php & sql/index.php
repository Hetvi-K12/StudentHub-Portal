<?php
$required_role = 'admin';
require_once __DIR__ . '/../MySQL3/auth.php';
require_once __DIR__ . '/db.php';

$events = [];
$result = $conn->query("SELECT id, name, description, event_date, venue, poster_path FROM events ORDER BY event_date ASC");
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Event Management — StudentHub Portal Practical 12">
    <title>Event Management — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Event Management</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="../MySQL3/admin_dashboard.php">Dashboard</a> &nbsp;
        <a href="add.php">Add Event</a> &nbsp;
        <a href="../MySQL3/logout.php">Logout</a> &nbsp;
    </nav>

    <?php if (isset($_GET['msg'])): ?>
    <div class="p12-msg <?php echo ($_GET['type'] ?? '') === 'err' ? 'p12-err' : 'p12-ok'; ?>">
        <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
    <?php endif; ?>

    <?php if (empty($events)): ?>
    <p class="p12-empty">No events found. <a href="add.php">Add the first event.</a></p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Poster</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $e): ?>
            <tr>
                <td><?php echo htmlspecialchars($e['name']); ?></td>
                <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                <td><?php echo htmlspecialchars($e['venue'] ?? '—'); ?></td>
                <td>
                    <?php if ($e['poster_path'] && file_exists(__DIR__ . '/' . $e['poster_path'])): ?>
                        <img src="<?php echo htmlspecialchars($e['poster_path']); ?>" alt="Poster" class="p12-thumb">
                    <?php else: ?>
                        No Poster
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit.php?id=<?php echo (int)$e['id']; ?>" class="p12-btn p12-btn-edit">Edit</a>
                    <a href="delete.php?id=<?php echo (int)$e['id']; ?>" class="p12-btn p12-btn-delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>
