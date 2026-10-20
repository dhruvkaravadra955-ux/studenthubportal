<?php
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

require "../db.php";

function respond($success, $message, $data = null, $status = 200) {
    http_response_code($status);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

function readInput() {
    $raw = file_get_contents("php://input");
    $json = json_decode($raw, true);
    if (is_array($json)) return $json;
    return $_POST;
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $search = trim($_GET["search"] ?? "");
    $term = "%" . $search . "%";
    $stmt = $conn->prepare(
        "SELECT id, name, email, course FROM students
         WHERE name LIKE ? OR email LIKE ? OR course LIKE ?
         ORDER BY id DESC"
    );
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    while ($row = $result->fetch_assoc()) $students[] = $row;
    respond(true, "Students loaded.", $students);
}

$input = readInput();

if ($method === "POST") {
    $action = $input["action"] ?? "add";
    $id = (int)($input["id"] ?? 0);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $course = trim($input["course"] ?? "");

    if ($name === "" || $email === "" || $course === "") {
        respond(false, "Please fill in all fields.", null, 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(false, "Please enter a valid email address.", null, 400);
    }
    if (!in_array($course, ["BCA", "BSc IT", "BTech"], true)) {
        respond(false, "Please select a valid course.", null, 400);
    }

    if ($action === "update") {
        if ($id < 1) respond(false, "Invalid student ID.", null, 400);
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, course=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $course, $id);
        if (!$stmt->execute()) {
            if ($conn->errno === 1062) respond(false, "That email is already in use.", null, 409);
            respond(false, "Could not update student.", null, 500);
        }
        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare("SELECT id FROM students WHERE id=?");
            $check->bind_param("i", $id);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            if (!$exists) respond(false, "Student not found.", null, 404);
        }
        respond(true, "Student updated successfully.");
    }

    if ($action === "add") {
        $stmt = $conn->prepare("INSERT INTO students(name, email, course) VALUES(?,?,?)");
        $stmt->bind_param("sss", $name, $email, $course);
        if (!$stmt->execute()) {
            if ($conn->errno === 1062) respond(false, "That email is already in use.", null, 409);
            respond(false, "Could not add student.", null, 500);
        }
        respond(true, "Student added successfully.", ["id" => $stmt->insert_id], 201);
    }

    respond(false, "Unknown action.", null, 400);
}

if ($method === "DELETE") {
    $id = (int)($input["id"] ?? $_GET["id"] ?? 0);
    if ($id < 1) respond(false, "Invalid student ID.", null, 400);

    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows < 1) respond(false, "Student not found.", null, 404);
    respond(true, "Student deleted successfully.");
}

header("Allow: GET, POST, DELETE");
respond(false, "Method not allowed.", null, 405);
?>