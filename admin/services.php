
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

// Load services from database
$result = $conn->query(
    "SELECT id, title, description, image, status, sort_order
     FROM services
     ORDER BY sort_order ASC, id ASC"
);

if (!$result) {
    die("Database error: " . $conn->error);
}

$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Services | NexaTech Admin</title>

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

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

h1 {
    color: #37517e;
}

.btn {
    display: inline-block;
    padding: 11px 18px;
    border-radius: 7px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    color: white;
}

.btn-add {
    background: #47b2e4;
}

.btn-edit {
    background: #37517e;
}

.btn-delete {
    background: #dc3545;
}

.btn-status {
    background: #198754;
}

.btn:hover {
    opacity: 0.9;
}

.table-wrapper {
    background: white;
    border-radius: 12px;
    padding: 20px;
    overflow-x: auto;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    background: #eef4fa;
    color: #37517e;
}

td {
    vertical-align: middle;
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.actions form {
    margin: 0;
}

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.active-status {
    background: #d4edda;
    color: #155724;
}

.inactive-status {
    background: #f8d7da;
    color: #721c24;
}

.empty {
    text-align: center;
    color: #777;
    padding: 25px;
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

    .header {
        flex-direction: column;
        align-items: flex-start;
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

    <a href="services.php" class="active">
        Services
    </a>

    <a href="portfolio.php">Portfolio</a>

    <a href="faq.php">FAQ</a>

    <a href="contact.php">Contact</a>

    <a href="../index.php" target="_blank">
        View Website
    </a>

</div>

<!-- Main Content -->
<div class="main">

    <div class="header">

        <div>
            <h1>Manage Services</h1>
            <p>Manage your website services dynamically.</p>
        </div>

        <a href="add-service.php" class="btn btn-add">
            + Add Service
        </a>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (empty($services)): ?>

                <tr>
                    <td colspan="6" class="empty">
                        No services found.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($services as $service): ?>

                    <tr>

                        <td>
                            <?= (int)$service["id"] ?>
                        </td>

                        <td>
                            <?= e($service["title"]) ?>
                        </td>

                        <td>
                            <?= e($service["description"]) ?>
                        </td>

                        <td>
                            <?= (int)$service["sort_order"] ?>
                        </td>

                        <td>

                            <?php if ((int)$service["status"] === 1): ?>

                                <span class="status active-status">
                                    Active
                                </span>

                            <?php else: ?>

                                <span class="status inactive-status">
                                    Inactive
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <div class="actions">

                                <!-- Edit Service -->
                                <a
                                    href="edit-service.php?id=<?= (int)$service['id'] ?>"
                                    class="btn btn-edit"
                                >
                                    Edit
                                </a>

                                <!-- Activate / Deactivate -->
                                <form
                                    action="toggle-service.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$service['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($_SESSION['csrf_token']) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-status"
                                    >
                                        <?= (int)$service["status"] === 1
                                            ? "Deactivate"
                                            : "Activate" ?>
                                    </button>

                                </form>

                                <!-- Delete without confirmation -->
                                <form
                                    action="delete-service.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$service['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($_SESSION['csrf_token']) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-delete"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
