<?php
require "db.php";

$msg = "";
$edit = null;
$uploadDir = __DIR__ . "/uploads/";
$allowedTypes = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];
$maxSize = 2 * 1024 * 1024;

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function logChange($conn, $eventId, $action, $details) {
    $check = $conn->query("SHOW TABLES LIKE 'event_audit'");
    if ($check && $check->num_rows > 0) {
        $s = $conn->prepare("INSERT INTO event_audit(event_id, action_name, details) VALUES(?,?,?)");
        $s->bind_param("iss", $eventId, $action, $details);
        $s->execute();
        $s->close();
    }
}

function savePoster($field, $uploadDir, $allowedTypes, $maxSize, &$error) {
    if (!isset($_FILES[$field]) || $_FILES[$field]["error"] === UPLOAD_ERR_NO_FILE) {
        return "";
    }

    $file = $_FILES[$field];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        $error = "Poster upload failed.";
        return false;
    }

    if ($file["size"] > $maxSize) {
        $error = "Poster must be 2 MB or smaller.";
        return false;
    }

    if (!is_uploaded_file($file["tmp_name"])) {
        $error = "Invalid uploaded file.";
        return false;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
    if (!isset($allowedTypes[$mime])) {
        $error = "Only JPG, PNG, and WEBP images are allowed.";
        return false;
    }

    if (!getimagesize($file["tmp_name"])) {
        $error = "The selected file is not a valid image.";
        return false;
    }

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        $error = "Upload folder could not be created.";
        return false;
    }

    $newName = bin2hex(random_bytes(12)) . "." . $allowedTypes[$mime];
    if (!move_uploaded_file($file["tmp_name"], $uploadDir . $newName)) {
        $error = "Could not save poster.";
        return false;
    }

    return $newName;
}

function deletePoster($filename, $uploadDir) {
    if ($filename !== "" && basename($filename) === $filename) {
        $path = $uploadDir . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    $id = (int)($_POST["id"] ?? 0);

    if ($action === "save") {
        $title = trim($_POST["title"] ?? "");
        $date = $_POST["event_date"] ?? "";
        $category = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $status = $_POST["status"] ?? "Open";
        $oldPoster = "";

        if ($title === "" || $date === "" || $category === "") {
            $msg = "Please fill all required fields.";
        } elseif (!in_array($status, ["Open", "Closed"], true)) {
            $msg = "Invalid event status.";
        } else {
            $posterError = "";
            $poster = savePoster("poster", $uploadDir, $allowedTypes, $maxSize, $posterError);

            if ($poster === false) {
                $msg = $posterError;
            } else {
                if ($id > 0) {
                    $s = $conn->prepare("SELECT poster FROM events WHERE id=?");
                    $s->bind_param("i", $id);
                    $s->execute();
                    $old = $s->get_result()->fetch_assoc();
                    $s->close();

                    if (!$old) {
                        $msg = "Event not found.";
                        if ($poster !== "") deletePoster($poster, $uploadDir);
                    } else {
                        $finalPoster = ($poster !== "") ? $poster : ($old["poster"] ?? "");
                        $s = $conn->prepare("UPDATE events SET title=?, event_date=?, category=?, description=?, status=?, poster=? WHERE id=?");
                        $s->bind_param("ssssssi", $title, $date, $category, $description, $status, $finalPoster, $id);

                        if ($s->execute()) {
                            $msg = "Event updated successfully.";
                            logChange($conn, $id, "UPDATE", "Updated event: " . $title);
                            if ($poster !== "") deletePoster($old["poster"] ?? "", $uploadDir);
                        } else {
                            $msg = "Update failed. Please check the details.";
                            if ($poster !== "") deletePoster($poster, $uploadDir);
                        }
                        $s->close();
                    }
                } else {
                    $s = $conn->prepare("INSERT INTO events(title, event_date, category, description, status, poster) VALUES(?,?,?,?,?,?)");
                    $s->bind_param("ssssss", $title, $date, $category, $description, $status, $poster);
                    if ($s->execute()) {
                        $newId = $s->insert_id;
                        $msg = "Event added successfully.";
                        logChange($conn, $newId, "ADD", "Added event: " . $title);
                    } else {
                        $msg = "Add failed. Please check the details.";
                        if ($poster !== "") deletePoster($poster, $uploadDir);
                    }
                    $s->close();
                }
            }
        }
    } elseif ($action === "delete" && $id > 0) {
        $s = $conn->prepare("SELECT poster, title FROM events WHERE id=?");
        $s->bind_param("i", $id);
        $s->execute();
        $old = $s->get_result()->fetch_assoc();
        $s->close();

        if ($old) {
            $s = $conn->prepare("DELETE FROM events WHERE id=?");
            $s->bind_param("i", $id);
            if ($s->execute() && $s->affected_rows > 0) {
                $msg = "Event deleted successfully.";
                logChange($conn, $id, "DELETE", "Deleted event: " . $old["title"]);
                deletePoster($old["poster"] ?? "", $uploadDir);
            } else {
                $msg = "Delete failed.";
            }
            $s->close();
        } else {
            $msg = "Event not found.";
        }
    }
}

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $s = $conn->prepare("SELECT * FROM events WHERE id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $edit = $s->get_result()->fetch_assoc();
    $s->close();
}

$search = trim($_GET["search"] ?? "");
$categoryFilter = trim($_GET["category"] ?? "");
$statusFilter = trim($_GET["status"] ?? "");
$term = "%" . $search . "%";

$sql = "SELECT * FROM events WHERE (title LIKE ? OR category LIKE OR description LIKE ?)"; // replaced below
$sql = "SELECT * FROM events WHERE (title LIKE ? OR category LIKE ? OR description LIKE ?)";
$params = [$term, $term, $term];
$types = "sss";

if ($categoryFilter !== "") {
    $sql .= " AND category=?";
    $params[] = $categoryFilter;
    $types .= "s";
}
if (in_array($statusFilter, ["Open", "Closed"], true)) {
    $sql .= " AND status=?";
    $params[] = $statusFilter;
    $types .= "s";
}
$sql .= " ORDER BY event_date DESC, id DESC";

$s = $conn->prepare($sql);
$s->bind_param($types, ...$params);
$s->execute();
$result = $s->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Event Management</h1>
        <p>Add, view, update and manage college events.</p>
    </header>

    <?php if ($msg !== ""): ?>
        <div class="message"><?= e($msg) ?></div>
    <?php endif; ?>

    <section class="panel">
        <h2><?= $edit ? "Update Event" : "Add New Event" ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= e($edit["id"] ?? 0) ?>">

            <label for="title">Event Name *</label>
            <input id="title" name="title" required maxlength="100" value="<?= e($edit["title"] ?? "") ?>">

            <label for="event_date">Event Date *</label>
            <input id="event_date" type="date" name="event_date" required value="<?= e($edit["event_date"] ?? "") ?>">

            <label for="category">Category *</label>
            <select id="category" name="category" required>
                <?php foreach (["Technical", "Cultural", "Workshop", "Sports", "Other"] as $c): ?>
                    <option value="<?= e($c) ?>" <?= (($edit["category"] ?? "") === $c) ? "selected" : "" ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= e($edit["description"] ?? "") ?></textarea>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="Open" <?= (($edit["status"] ?? "Open") === "Open") ? "selected" : "" ?>>Open</option>
                <option value="Closed" <?= (($edit["status"] ?? "") === "Closed") ? "selected" : "" ?>>Closed</option>
            </select>

            <label for="poster">Poster (JPG, PNG, WEBP; max 2 MB)</label>
            <input id="poster" type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <?php if (!empty($edit["poster"])): ?>
                <p>Current poster:</p>
                <img class="small-poster" src="uploads/<?= e($edit["poster"]) ?>" alt="Current event poster">
            <?php endif; ?>

            <div class="buttons">
                <button type="submit"><?= $edit ? "Update Event" : "Add Event" ?></button>
                <?php if ($edit): ?><a class="button secondary" href="index.php">Cancel</a><?php endif; ?>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>Event Records</h2>
        <form method="GET" class="filters">
            <input name="search" placeholder="Search event or description" value="<?= e($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach (["Technical", "Cultural", "Workshop", "Sports", "Other"] as $c): ?>
                    <option value="<?= e($c) ?>" <?= $categoryFilter === $c ? "selected" : "" ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status">
                <option value="">All Statuses</option>
                <option value="Open" <?= $statusFilter === "Open" ? "selected" : "" ?>>Open</option>
                <option value="Closed" <?= $statusFilter === "Closed" ? "selected" : "" ?>>Closed</option>
            </select>
            <button type="submit">Search / Filter</button>
            <a class="button secondary" href="index.php">Clear</a>
        </form>

        <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Poster</th><th>Event</th><th>Date</th><th>Category</th><th>Status</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="8">No events found.</td></tr>
            <?php endif; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row["id"] ?></td>
                    <td>
                        <?php if (!empty($row["poster"])): ?>
                            <img class="poster" src="uploads/<?= e($row["poster"]) ?>" alt="Event poster">
                        <?php else: ?>No poster<?php endif; ?>
                    </td>
                    <td><?= e($row["title"]) ?></td>
                    <td><?= e($row["event_date"]) ?></td>
                    <td><?= e($row["category"]) ?></td>
                    <td><span class="status <?= strtolower(e($row["status"])) ?>"><?= e($row["status"]) ?></span></td>
                    <td><?= e($row["description"]) ?></td>
                    <td class="actions">
                        <a href="?edit=<?= (int)$row["id"] ?>">Edit</a>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">
                            <button class="danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </section>
    <footer>Practical 12 · Event Management CRUD</footer>
</div>
</body>
</html>
<?php
$s->close();
$conn->close();
?>