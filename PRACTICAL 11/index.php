
<?php
session_start();
require "db.php";

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

$message = "";
$editStudent = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page.");
    }

    $action = $_POST["action"] ?? "";

    if ($action == "add" || $action == "update") {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $course = trim($_POST["course"] ?? "");
        $year = filter_var($_POST["year"] ?? "", FILTER_VALIDATE_INT);

        if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $course == "" || $year === false
            || $year < 1 || $year > 6) {
            $message = "Please enter valid student details.";
        } else {
            if ($action == "add") {
                $stmt = $conn->prepare(
                    "INSERT INTO students (name,email,course,year)
                     VALUES (?,?,?,?)"
                );
                $stmt->bind_param("sssi", $name, $email, $course, $year);
            } else {
                $id = (int)($_POST["id"] ?? 0);
                $stmt = $conn->prepare(
                    "UPDATE students SET name=?, email=?, course=?, year=?
                     WHERE id=?"
                );
                $stmt->bind_param("sssii", $name, $email, $course, $year, $id);
            }

            if ($stmt->execute()) {
                $message = $action == "add"
                    ? "Student added successfully."
                    : ($stmt->affected_rows >= 0
                        ? "Student updated successfully."
                        : "Update failed.");
            } else {
                $message = $stmt->errno == 1062
                    ? "This email already exists."
                    : "Operation failed.";
            }
        }
    }

    if ($action == "delete") {
        $id = (int)($_POST["id"] ?? 0);
        $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $message = "Student deleted successfully.";
        } else {
            $message = "Student not found or delete failed.";
        }
    }

    $_SESSION["flash"] = $message;
    header("Location: index.php");
    exit;
}

if (isset($_SESSION["flash"])) {
    $message = $_SESSION["flash"];
    unset($_SESSION["flash"]);
}

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editStudent = $stmt->get_result()->fetch_assoc();
}

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");

$where = [];
$params = [];
$types = "";

if ($search != "") {
    $where[] = "(name LIKE ? OR email LIKE ? OR course LIKE ?)";
    $term = "%".$search."%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $types .= "sss";
}

if ($courseFilter != "") {
    $where[] = "course = ?";
    $params[] = $courseFilter;
    $types .= "s";
}

$sqlWhere = $where ? " WHERE ".implode(" AND ", $where) : "";

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM students".$sqlWhere
);
if ($types != "") {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()["total"];

$perPage = 5;
$pages = max(1, (int)ceil($total / $perPage));
$page = max(1, min($pages, (int)($_GET["page"] ?? 1)));
$offset = ($page - 1) * $perPage;

$listStmt = $conn->prepare(
    "SELECT * FROM students".$sqlWhere." ORDER BY id DESC LIMIT ? OFFSET ?"
);
$listTypes = $types."ii";
$listParams = array_merge($params, [$perPage, $offset]);
$listStmt->bind_param($listTypes, ...$listParams);
$listStmt->execute();
$students = $listStmt->get_result();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function pageUrl($p) {
    $q = $_GET;
    $q["page"] = $p;
    return "?".http_build_query($q);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Student Management System</h1>

<?php if ($message != ""): ?>
    <p class="message"><?= e($message) ?></p>
<?php endif; ?>

<h2><?= $editStudent ? "Edit Student" : "Add Student" ?></h2>

<form method="POST">
    <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
    <input type="hidden" name="action"
           value="<?= $editStudent ? "update" : "add" ?>">

    <?php if ($editStudent): ?>
        <input type="hidden" name="id" value="<?= e($editStudent["id"]) ?>">
    <?php endif; ?>

    <label>Name</label>
    <input type="text" name="name" required maxlength="100"
           value="<?= e($editStudent["name"] ?? "") ?>">

    <label>Email</label>
    <input type="email" name="email" required maxlength="100"
           value="<?= e($editStudent["email"] ?? "") ?>">

    <label>Course</label>
    <select name="course" required>
        <option value="">Select Course</option>
        <?php foreach (["BCA", "BSc IT", "BTech"] as $c): ?>
            <option value="<?= e($c) ?>"
                <?= (($editStudent["course"] ?? "") == $c) ? "selected" : "" ?>>
                <?= e($c) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Year</label>
    <input type="number" name="year" min="1" max="6" required
           value="<?= e($editStudent["year"] ?? "") ?>">

    <button type="submit">
        <?= $editStudent ? "Update Student" : "Add Student" ?>
    </button>

    <?php if ($editStudent): ?>
        <a href="index.php">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr>

<h2>Student Records</h2>

<form method="GET">
    <input type="text" name="search"
           placeholder="Search name, email or course"
           value="<?= e($search) ?>">

    <select name="course">
        <option value="">All Courses</option>
        <?php foreach (["BCA", "BSc IT", "BTech"] as $c): ?>
            <option value="<?= e($c) ?>"
                <?= $courseFilter == $c ? "selected" : "" ?>>
                <?= e($c) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Search / Filter</button>
    <a href="index.php">Clear</a>
    <a href="export.php">Export CSV</a>
</form>

<p>Total matching students: <?= (int)$total ?></p>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
        <th>Year</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $students->fetch_assoc()): ?>
    <tr>
        <td><?= e($row["id"]) ?></td>
        <td><?= e($row["name"]) ?></td>
        <td><?= e($row["email"]) ?></td>
        <td><?= e($row["course"]) ?></td>
        <td><?= e($row["year"]) ?></td>
        <td>
            <a href="?edit=<?= (int)$row["id"] ?>">Edit</a>

            <form method="POST" class="delete-form"
                  onsubmit="return confirm('Delete this student?');">
                <input type="hidden" name="csrf"
                       value="<?= e($_SESSION["csrf"]) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id"
                       value="<?= (int)$row["id"] ?>">
                <button type="submit">Delete</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<p>
    <?php if ($page > 1): ?>
        <a href="<?= e(pageUrl($page - 1)) ?>">Previous</a>
    <?php endif; ?>

    Page <?= $page ?> of <?= $pages ?>

    <?php if ($page < $pages): ?>
        <a href="<?= e(pageUrl($page + 1)) ?>">Next</a>
    <?php endif; ?>
</p>

</body>
</html>
