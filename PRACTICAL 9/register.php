
<?php
$conn = new mysqli("localhost", "root", "", "practical9");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $username == "" || strlen($password) < 8) {
        $message = "Please enter valid details. Password must be at least 8 characters.";
    } else {
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? OR username = ?"
        );
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email or username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, username, password)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $name, $email, $username, $hash);

            if ($stmt->execute()) {
                $message = "Registration successful!";
            } else {
                $message = "Registration failed.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h2>User Registration</h2>

    <form method="post">
        Name:
        <input type="text" name="name" required><br><br>

        Email:
        <input type="email" name="email" required><br><br>

        Username:
        <input type="text" name="username" required><br><br>

        Password:
        <input type="password" name="password"
               minlength="8" required><br><br>

        <button type="submit">Register</button>
    </form>

    <p><?php echo htmlspecialchars($message); ?></p>
</body>
</html>
