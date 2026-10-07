<?php
// =======================================================================
// STUDENTHUB PORTAL - PRACTICAL 8 PDO DATABASE CONNECTION (db.php)
// =======================================================================

$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    // Hide sensitive DB details from end-users
    die("Database connection failed.");
}
?>
