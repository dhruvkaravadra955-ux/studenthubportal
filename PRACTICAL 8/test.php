    
<?php
include 'db.php';

$sql = "SELECT * FROM students WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute(["dhruv@gmail.com"]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "<br>Student Name: " . htmlspecialchars($result['name']);
    echo "<br>Email: " . htmlspecialchars($result['email']);
}
?>
