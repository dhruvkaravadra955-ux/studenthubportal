<?php
require "db.php";

if (isset($_GET["logout"])) {
    if (!empty($_SESSION["user_id"])) {
        $uid = (int)$_SESSION["user_id"];
        $name = $_SESSION["name"] ?? "User";
        $action = "LOGOUT";
        $details = "User logged out";
        $log = $conn->prepare("INSERT INTO audit_logs(user_id, username, action, details) VALUES(?,?,?,?)");
        $log->bind_param("isss", $uid, $name, $action, $details);
        $log->execute();
    }
    session_destroy();
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    echo "<h1>403 - Access Denied</h1><p>Only admin users can access this dashboard.</p><a href='login.php?logout=1'>Logout</a>";
    exit;
}

$totalUsers = (int)$conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$totalStudents = (int)$conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()["total"];
$totalEvents = (int)$conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()["total"];
$totalRegistrations = (int)$conn->query("SELECT COUNT(*) AS total FROM registrations")->fetch_assoc()["total"];

$latest = $conn->query("SELECT id, student_name, email, event_name, registered_at FROM registrations ORDER BY registered_at DESC LIMIT 5");
$logs = $conn->query("SELECT username, action, details, created_at FROM audit_logs ORDER BY id DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <a class="active" href="index.php">Dashboard</a>
        <a href="#registrations">Latest Registrations</a>
        <a href="#audit">Audit Log</a>
        <a href="index.php?logout=1">Logout</a>
        <p class="role">Logged in as <?= htmlspecialchars($_SESSION["name"], ENT_QUOTES, "UTF-8") ?> (Admin)</p>
    </aside>
    <main class="main">
        <div class="top">
            <div><h1>Dashboard</h1><p>Overview of users, students, events and registrations.</p></div>
            <span class="admin-tag">ADMIN</span>
        </div>

        <section class="stats">
            <div class="stat"><p>Total Users</p><h2><?= $totalUsers ?></h2></div>
            <div class="stat"><p>Total Students</p><h2><?= $totalStudents ?></h2></div>
            <div class="stat"><p>Total Events</p><h2><?= $totalEvents ?></h2></div>
            <div class="stat"><p>Registrations</p><h2><?= $totalRegistrations ?></h2></div>
        </section>

        <section class="panel" id="registrations">
            <h2>Latest Registrations</h2>
            <div class="table-wrap"><table>
                <tr><th>ID</th><th>Student</th><th>Email</th><th>Event</th><th>Date</th></tr>
                <?php if ($latest->num_rows === 0): ?><tr><td colspan="5">No registrations found.</td></tr><?php endif; ?>
                <?php while ($r = $latest->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$r["id"] ?></td>
                    <td><?= htmlspecialchars($r["student_name"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($r["email"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($r["event_name"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($r["registered_at"], ENT_QUOTES, "UTF-8") ?></td>
                </tr>
                <?php endwhile; ?>
            </table></div>
        </section>

        <section class="panel" id="audit">
            <h2>Recent Audit Log</h2>
            <div class="table-wrap"><table>
                <tr><th>User</th><th>Action</th><th>Details</th><th>Time</th></tr>
                <?php if ($logs->num_rows === 0): ?><tr><td colspan="4">No actions recorded yet.</td></tr><?php endif; ?>
                <?php while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($log["username"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($log["action"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($log["details"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($log["created_at"], ENT_QUOTES, "UTF-8") ?></td>
                </tr>
                <?php endwhile; ?>
            </table></div>
        </section>
    </main>
</div>
</body>
</html>