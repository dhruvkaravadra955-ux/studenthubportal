<?php
require "db.php";
if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
if (($_SESSION["role"] ?? "") === "admin") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Student Page</title><link rel="stylesheet" href="style.css"></head>
<body class="login-body">
<div class="login-card">
    <h1>Student Page</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION["name"], ENT_QUOTES, "UTF-8") ?>.</p>
    <p>Your role is Student, so admin dashboard options are not available.</p>
    <a class="button" href="login.php?logout=1">Logout</a>
</div>
</body>
</html>