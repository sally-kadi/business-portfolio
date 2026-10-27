
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
$description = "";
$image = "";
$sortOrder = 5;
$status = 1;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    $token = $_POST["csrf_token"] ?? "";

    if (
        empty($_SESSION["csrf_token"]) ||
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page.");
    }

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $image = trim($_POST["image"] ?? "");

    $sortOrder = filter_var(
        $_POST["sort_order"] ?? null,
        FILTER_VALIDATE_INT
    );

    $status = ($_POST["status"] ?? "0") === "1" ? 1 : 0;

    if ($title === "" || $description === "") {
        $error = "Title and description are required.";

    } elseif (strlen($title) > 255) {
        $error = "Title must not exceed 255 characters.";

    } elseif ($sortOrder === false || $sortOrder < 0) {
        $error = "Please enter a valid display order.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO services
             (title, description, image, status, sort_order)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssii",
            $title,
            $description,
            $image,
            $status,
            $sortOrder
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: services.php?added=1");
            exit;

        } else {

            $error = "Unable to add service.";
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

<title>Add Service | NexaTech Admin</title>

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

<!-- Sidebar -->
<div class="sidebar">

    <h2>NexaTech Admin</h2>

    <a href="index.php">Dashboard</a>
    <a href="sections.php">Sections</a>
    <a href="services.php" class="active">Services</a>
    <a href="portfolio.php">Portfolio</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact</a>

    <a href="../index.php" target="_blank">
        View Website
    </a>

</div>

<!-- Main Content -->
<div class="main">

    <h1>Add New Service</h1>

    <p class="subtitle">
        Create a new service for your business portfolio.
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
                <label for="title">Service Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= e($title) ?>"
                    maxlength="255"
                    placeholder="Example: SEO Optimization"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe the service..."
                    required
                ><?= e($description) ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Image Path</label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?= e($image) ?>"
                    placeholder="assets/images/service.jpg"
                >
            </div>

            <div class="form-group">
                <label for="sort_order">Display Order</label>

                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    min="0"
                    value="<?= e($sortOrder) ?>"
                    required
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

                <button type="submit" class="btn btn-save">
                    Add Service
                </button>

                <a href="services.php" class="btn btn-back">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>
