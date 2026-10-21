<?php

$host     = "localhost";
$dbname   = "studenthub";
$username = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Database connection failed. Please try again later.");
}

$mysqli->set_charset("utf8mb4");
?>
