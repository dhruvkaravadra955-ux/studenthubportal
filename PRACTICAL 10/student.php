
<?php
session_start();

if (!isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "student") {
    http_response_code(403);
    exit("Access denied. Students only.");
}

if (time() - $_SESSION["last_activity"] > 60) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION["last_activity"] = time();
?>

<h2>Student Dashboard</h2>
<p>Welcome, Student!</p>
<a href="logout.php">Logout</a>
