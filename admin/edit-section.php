
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$error = "";

// Check section ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit("Invalid section ID.");
}

// Get section from database
$stmt = $conn->prepare(
    "SELECT * FROM sections WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$section = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$section) {
    http_response_code(404);
    exit("Section not found.");
}

// Update section
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page.");
    }

    $title = trim($_POST["title"] ?? "");
    $subtitle = trim($_POST["subtitle"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $image = trim($_POST["image"] ?? "");
    $status = ($_POST["status"] ?? "0") === "1" ? 1 : 0;

    if ($title === "") {

        $error = "Title is required.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE sections
             SET title = ?,
                 subtitle = ?,
                 description = ?,
                 image = ?,
                 status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssii",
            $title,
            $subtitle,
            $description,
            $image,
            $status,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();

            header("Location: sections.php?updated=1");
            exit;
        } else {
            $error = "Unable to update section.";
            $stmt->close();
        }
    }

    // Keep submitted values if validation fails
    $section["title"] = $title;
    $section["subtitle"] = $subtitle;
    $section["description"] = $description;
    $section["image"] = $image;
    $section["status"] = $status;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Section | NexaTech Admin</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    color: #333;
}

.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: #37517e;
    padding: 30px 20px;
    overflow-y: auto;
}

.sidebar h2 {
    color: white;
    text-align: center;
    margin-bottom: 35px;
}

.sidebar a {
    display: block;
    padding: 14px 18px;
    margin-bottom: 10px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #47b2e4;
}

.main {
    margin-left: 250px;
    padding: 40px;
}

h1 {
    color: #37517e;
    margin-bottom: 10px;
}

.subtitle {
    color: #777;
    margin-bottom: 30px;
}

.form-card {
    background: white;
    max-width: 800px;
    padding: 35px;
    border-radius: 14px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
}

.form-group {
    margin-bottom: 22px;
}

label {
    display: block;
    margin-bottom: 9px;
    font-weight: bold;
    color: #37517e;
}

input,
textarea,
select {
    width: 100%;
    padding: 13px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    font-family: Arial, sans-serif;
}

input:focus,
textarea:focus,
select:focus {
    outline: none;
    border-color: #47b2e4;
}

textarea {
    min-height: 130px;
    resize: vertical;
}

.btn {
    display: inline-block;
    padding: 13px 22px;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
}

.btn-save {
    background: #47b2e4;
    color: white;
}

.btn-back {
    background: #e9ecef;
    color: #333;
    margin-left: 10px;
}

.error {
    background: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

@media(max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 20px;
    }

    .form-card {
        padding: 20px;
    }
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>NexaTech Admin</h2>

    <a href="index.php">Dashboard</a>
    <a href="sections.php" class="active">Sections</a>
    <a href="services.php">Services</a>
    <a href="portfolio.php">Portfolio</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact</a>
    <a href="../index.php" target="_blank">View Website</a>
</div>

<div class="main">

    <h1>Edit Section</h1>

    <p class="subtitle">
        Update your website section content.
    </p>

    <div class="form-card">

        <?php if ($error): ?>
            <div class="error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION["csrf_token"]) ?>"
            >

            <div class="form-group">
                <label>Section Key</label>

                <input
                    type="text"
                    value="<?= e($section['section_key']) ?>"
                    readonly
                >
            </div>

            <div class="form-group">
                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    value="<?= e($section['title']) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Subtitle</label>

                <input
                    type="text"
                    name="subtitle"
                    value="<?= e($section['subtitle']) ?>"
                >
            </div>

            <div class="form-group">
                <label>Description</label>

                <textarea name="description"><?= e(
                    $section['description']
                ) ?></textarea>
            </div>

            <div class="form-group">
                <label>Image Path</label>

                <input
                    type="text"
                    name="image"
                    value="<?= e($section['image']) ?>"
                    placeholder="assets/images/example.png"
                >
            </div>

            <div class="form-group">
                <label>Status</label>

                <select name="status">
                    <option value="1"
                        <?= (int)$section["status"] === 1
                            ? "selected" : "" ?>>
                        Active
                    </option>

                    <option value="0"
                        <?= (int)$section["status"] === 0
                            ? "selected" : "" ?>>
                        Inactive
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-save">
                Save Changes
            </button>

            <a href="sections.php" class="btn btn-back">
                Cancel
            </a>

        </form>

    </div>
</div>

</body>
</html>
