
<?php
require "db.php";

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=students.csv");

$output = fopen("php://output", "w");

fputcsv($output, ["ID", "Name", "Email", "Course", "Year"]);

$result = $conn->query(
    "SELECT id, name, email, course, year FROM students ORDER BY id"
);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, array_values($row));
}

fclose($output);
$conn->close();
exit;
?>
