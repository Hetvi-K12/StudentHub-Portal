<?php

$host     = "localhost";
$dbname   = "studenthub";
$username = "root";
$password = "";

// Create a MySQLi connection object
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check if the connection failed
if ($mysqli->connect_error) {
    // Hide internal details from the browser; log if needed
    die("Database connection failed. Please try again later.");
}

// Set character encoding to utf8mb4 for full Unicode support
$mysqli->set_charset("utf8mb4");
?>
