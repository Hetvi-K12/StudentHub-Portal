<?php
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $result  = $conn->query("SELECT id, name, description, icon FROM courses ORDER BY id ASC");
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $courses]);

    } elseif ($method === 'POST') {
        $input       = json_decode(file_get_contents('php://input'), true) ?? [];
        $name        = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $icon        = trim($input['icon'] ?? 'fas fa-book');

        if ($name === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course name is required.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO courses (name, description, icon) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $icon);
        $stmt->execute();
        $id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Course added.',
            'data'    => ['id' => $id, 'name' => $name, 'description' => $description, 'icon' => $icon]
        ]);

    } elseif ($method === 'PUT') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
            exit;
        }

        $input       = json_decode(file_get_contents('php://input'), true) ?? [];
        $name        = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $icon        = trim($input['icon'] ?? 'fas fa-book');

        if ($name === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course name is required.']);
            exit;
        }

        $check = $conn->prepare("SELECT id FROM courses WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Course not found.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE courses SET name=?, description=?, icon=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $description, $icon, $id);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Course updated.']);

    } elseif ($method === 'DELETE') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Course not found.']);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Course deleted.']);

    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Check that MySQL is running and the courses table exists. Run MySQL6/database.sql if needed.']);
}
