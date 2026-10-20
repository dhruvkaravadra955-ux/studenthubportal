<?php
$conn = new mysqli("localhost", "root", "", "practical12");

if ($conn->connect_error) {
    die("Database connection failed. Please check XAMPP and database name.");
}

$conn->set_charset("utf8mb4");
?>