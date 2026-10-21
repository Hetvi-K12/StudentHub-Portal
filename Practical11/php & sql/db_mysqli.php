<?php
$host     = "localhost";
$dbname   = "studenthub";
$username = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $username, $password, $dbname);
    $mysqli->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Friendly output – do not expose sensitive details in production
    die("<h2 style='color:#c00;'>Database connection failed. Please ensure MySQL server is running and the credentials are correct.</h2>");
}

