<?php
require "db.php";
$error = "";

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        $uid = (int)$user["id"];
        $name = $user["name"];
        $action = "LOGIN";
        $details = "Successful login as " . $user["role"];
        $log = $conn->prepare("INSERT INTO audit_logs(user_id, username, action, details) VALUES(?,?,?,?)");
        $log->bind_param("isss", $uid, $name, $action, $details);
        $log->execute();

        if ($user["role"] === "admin") {
            header("Location: index.php");
        } else {
            header("Location: student.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
<div class="login-card">
    <h1>Admin Login</h1>
    <p>Sign in to continue</p>
    <?php if ($error !== ""): ?><div class="error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></div><?php endif; ?>
    <form method="POST">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
   
</div>
</body>
</html>