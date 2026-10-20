<?php
session_start();
$conn = new mysqli("localhost", "root", "", "practical14");
if ($conn->connect_error) {
    die("Database connection failed. Check XAMPP and practical14 database.");
}
$conn->set_charset("utf8mb4");
?>