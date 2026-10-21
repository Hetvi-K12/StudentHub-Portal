<?php
// =======================================================================
// STUDENTHUB PORTAL - PRACTICAL 8 CAMPUS EVENTS & REGISTRATION (events.php)
// =======================================================================

session_start();
require_once __DIR__ . '/db.php';

$message = '';
$status = '';

// Check if user is logged in
$is_logged_in = isset($_SESSION['student_id']);
$student_id = $_SESSION['student_id'] ?? null;

// Handle Event Registration Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    if (!$is_logged_in) {
        $status = 'error';
        $message = 'Please login before registering for an event.';
    } else {
        $event_id = filter_var($_POST['event_id'], FILTER_VALIDATE_INT);

        if (!$event_id) {
            $status = 'error';
            $message = 'Invalid event selected.';
        } else {
            try {
                // 1. Verify event exists
                $eventStmt = $pdo->prepare("SELECT id, name FROM events WHERE id = :id");
                $eventStmt->execute([':id' => $event_id]);
                $event = $eventStmt->fetch();

                if (!$event) {
                    $status = 'error';
                    $message = 'Selected event does not exist.';
                } else {
                    // 2. Check for duplicate registration using Prepared Statement
                    $checkStmt = $pdo->prepare(
                        "SELECT id FROM registrations WHERE student_id = :student_id AND event_id = :event_id"
                    );
                    $checkStmt->execute([
                        ':student_id' => $student_id,
                        ':event_id' => $event_id
                    ]);

                    if ($checkStmt->fetch()) {
                        $status = 'error';
                        $message = 'You are already registered for this event.';
                    } else {
                        // 3. Insert registration record into database using Prepared Statement
                        $insertStmt = $pdo->prepare(
                            "INSERT INTO registrations (student_id, event_id) VALUES (:student_id, :event_id)"
                        );
                        $insertStmt->execute([
                            ':student_id' => $student_id,
                            ':event_id' => $event_id
                        ]);

                        $status = 'success';
                        $message = 'Successfully registered for ' . htmlspecialchars($event['name']) . '!';
                    }
                }
            } catch (PDOException $e) {
                $status = 'error';
                $message = 'Database error occurred while processing registration.';
            }
        }
    }
}

// Fetch all events from database using Prepared Statement
$eventsStmt = $pdo->prepare("SELECT * FROM events ORDER BY event_date ASC");
$eventsStmt->execute();
$events = $eventsStmt->fetchAll();

// Fetch student's registered event IDs if logged in
$registered_event_ids = [];
if ($is_logged_in) {
    $regStmt = $pdo->prepare("SELECT event_id FROM registrations WHERE student_id = :student_id");
    $regStmt->execute([':student_id' => $student_id]);
    $registered_event_ids = $regStmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events - StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Campus Events</h1>

    <nav>
        <a href="index.html">Home</a> &nbsp;
        <?php if ($is_logged_in): ?>
            <a href="dashboard.php">Dashboard</a> &nbsp;
            <a href="events.php">Events</a> &nbsp;
        <?php else: ?>
            <a href="login.php">Login</a> &nbsp;
            <a href="register.php">Register</a> &nbsp;
        <?php endif; ?>
        <a href="about.html">About</a> &nbsp;
        <a href="contact.html">Contact</a>
    </nav>
    <br>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo htmlspecialchars($status); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div style="width: 95%; margin: 20px auto; background: transparent; box-shadow: none;">
        <h2>Upcoming Events</h2>
        <p>Explore official StudentHub events and register using your student account.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Event Name</th>
                <th>Description</th>
                <th>Event Date</th>
                <th>Action / Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($events)): ?>
                <tr>
                    <td colspan="5">No upcoming events found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($event['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($event['description']); ?></td>
                        <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td>
                            <?php if ($is_logged_in): ?>
                                <?php if (in_array($event['id'], $registered_event_ids)): ?>
                                    <span style="color: green; font-weight: bold;">Registered &#10004;</span>
                                <?php else: ?>
                                    <form action="events.php" method="POST" style="margin:0; padding:0; width:auto; background:none; box-shadow:none;">
                                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                                        <button type="submit" style="padding: 6px 16px; font-size: 14px;">Register</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" style="color: #16233B; font-weight: bold; text-decoration: underline;">Login to Register</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="script.js"></script>
</body>
</html>
