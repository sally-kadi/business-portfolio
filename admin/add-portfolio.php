
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Escape HTML output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$error = "";

$title = "";
$category = "web";
$image = "";
$status = 1;

$allowedCategories = ["web", "app", "marketing"];

// Handle form submission
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

    // Read form values
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $image = trim($_POST["image"] ?? "");
    $status = ($_POST["status"] ?? "0") === "1" ? 1 : 0;

    // Validate input
    if ($title === "") {

        $error = "Project title is required.";

    } elseif (strlen($title) > 255) {

        $error = "Project title is too long.";

    } elseif (!in_array($category, $allowedCategories, true)) {

        $error = "Please select a valid category.";

    } elseif (strlen($image) > 2048) {

        $error = "Image path is too long.";

    } else {

        // Insert new project
        $stmt = $conn->prepare(
            "INSERT INTO portfolio_items
                (title, category, image, status)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssi",
            $title,
            $category,
            $image,
            $status
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: portfolio.php?added=1");
            exit;

        } else {

            $error = "Unable to add project.";
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Portfolio Project | NexaTech Admin</title>

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
    transition: 0.3s;
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
select {
    width: 100%;
    padding: 13px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    font-family: Arial, sans-serif;
}

input:focus,
select:focus {
    outline: none;
    border-color: #47b2e4;
}

.buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
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
}

.btn:hover {
    opacity: 0.9;
}

.error {
    background: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
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

<!-- Sidebar -->
<div class="sidebar">

    <h2>NexaTech Admin</h2>

    <a href="index.php">Dashboard</a>
    <a href="sections.php">Sections</a>
    <a href="services.php">Services</a>
    <a href="portfolio.php" class="active">Portfolio</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact</a>

    <a href="../index.php" target="_blank">
        View Website
    </a>

</div>

<!-- Main Content -->
<div class="main">

    <h1>Add Portfolio Project</h1>

    <p class="subtitle">
        Add a new project to your business portfolio.
    </p>

    <div class="form-card">

        <?php if ($error !== ""): ?>

            <div class="error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <!-- CSRF Protection -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION["csrf_token"]) ?>"
            >

            <div class="form-group">

                <label for="title">Project Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= e($title) ?>"
                    placeholder="Enter project title"
                    maxlength="255"
                    required
                >

            </div>

            <div class="form-group">

                <label for="category">Category</label>

                <select id="category" name="category">

                    <option
                        value="web"
                        <?= $category === "web" ? "selected" : "" ?>
                    >
                        Web
                    </option>

                    <option
                        value="app"
                        <?= $category === "app" ? "selected" : "" ?>
                    >
                        App
                    </option>

                    <option
                        value="marketing"
                        <?= $category === "marketing" ? "selected" : "" ?>
                    >
                        Marketing
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label for="image">Image URL / Path</label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?= e($image) ?>"
                    placeholder="assets/images/project.jpg"
                >

            </div>

            <div class="form-group">

                <label for="status">Status</label>

                <select id="status" name="status">

                    <option
                        value="1"
                        <?= $status === 1 ? "selected" : "" ?>
                    >
                        Active
                    </option>

                    <option
                        value="0"
                        <?= $status === 0 ? "selected" : "" ?>
                    >
                        Inactive
                    </option>

                </select>

            </div>

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-save"
                >
                    Add Project
                </button>

                <a
                    href="portfolio.php"
                    class="btn btn-back"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>
