<?php
$host     = "localhost";
$dbname   = "studenthub";
$username = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}
