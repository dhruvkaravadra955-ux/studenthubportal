
<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, username, password, role FROM users WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            $update = $conn->prepare(
                "UPDATE users SET last_login = NOW() WHERE id = ?"
            );
            $update->bind_param("i", $user["id"]);
            $update->execute();

            if ($user["role"] == "admin") {
                header("Location: admin.php");
            } else {
                header("Location: student.php");
            }
            exit;
        }
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Login</title>
</head>
<body>
    <h2>Login</h2>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br><br>

        <label>Password:</label>
        <input type="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>
    </form>

    <p style="color:red;">
        <?php echo htmlspecialchars($error); ?>
    </p>
</body>
</html>
