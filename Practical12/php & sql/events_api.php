<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

try {
    $stmt = $pdo->prepare("SELECT id, name, description, event_date FROM events ORDER BY event_date ASC");
    $stmt->execute();
    $events = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $events]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch events from database.']);
}
?>
