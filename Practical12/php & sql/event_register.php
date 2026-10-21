<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['student_id'])) {
        die("Please login before registering for an event.");
    }

    $student_id = $_SESSION['student_id'];
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

    if ($event_id <= 0) {
        die("Invalid event selection.");
    }

    try {
        // Check for duplicate registration
        $checkStmt = $pdo->prepare("SELECT id FROM registrations WHERE student_id = :student_id AND event_id = :event_id");
        $checkStmt->execute([':student_id' => $student_id, ':event_id' => $event_id]);

        if ($checkStmt->fetch()) {
            die("You are already registered for this event.");
        }

        // Insert registration record using PDO prepared statement
        $insertStmt = $pdo->prepare("INSERT INTO registrations (student_id, event_id) VALUES (:student_id, :event_id)");
        $insertStmt->execute([':student_id' => $student_id, ':event_id' => $event_id]);

        header("Location: events.php");
        exit;
    } catch (PDOException $e) {
        die("Database error during event registration.");
    }
} else {
    header("Location: events.php");
    exit;
}
?>
