
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

// Load FAQs
$result = $conn->query(
    "SELECT id, question, answer, status
     FROM faqs
     ORDER BY id ASC"
);

if (!$result) {
    die("Unable to load FAQs.");
}

$faqs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage FAQ | NexaTech Admin</title>

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

/* Sidebar */
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

/* Main */
.main {
    margin-left: 250px;
    padding: 40px;
    min-width: 0;
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
    margin: 0 0 10px;
}

.header p {
    color: #6c757d;
    margin: 0;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 10px 13px;
    border: none;
    border-radius: 7px;
    text-decoration: none;
    cursor: pointer;
    font-size: 12px;
    font-family: Arial, sans-serif;
    color: white;
    text-align: center;
    white-space: nowrap;
}

.btn-add {
    background: #47b2e4;
    padding: 11px 18px;
    font-size: 14px;
}

.btn-edit {
    background: #37517e;
}

.btn-status {
    background: #198754;
}

.btn-delete {
    background: #dc3545;
}

.btn:hover {
    opacity: 0.85;
}

/* Table */
.table-wrapper {
    background: white;
    border-radius: 12px;
    padding: 20px;
    overflow-x: auto;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

table {
    width: 100%;
    min-width: 1100px;
    border-collapse: collapse;
}

th,
td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

th {
    background: #eef4fa;
    color: #37517e;
    white-space: nowrap;
    font-size: 14px;
}

td {
    font-size: 14px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

tbody tr:hover {
    background: #fafcff;
}

tbody tr:last-child td {
    border-bottom: none;
}

th:nth-child(1) {
    width: 65px;
}

th:nth-child(2) {
    width: 230px;
}

th:nth-child(4) {
    width: 110px;
}

th:nth-child(5) {
    width: 365px;
}

/* Status */
.status {
    display: inline-block;
    min-width: 78px;
    padding: 6px 12px;
    border-radius: 20px;
    text-align: center;
    white-space: nowrap;
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

/* Actions */
.actions {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
    flex-wrap: nowrap;
    white-space: nowrap;
}

.actions form {
    margin: 0;
    display: inline-flex;
    flex-shrink: 0;
}

.actions .btn {
    padding: 10px 12px;
    font-size: 12px;
}

/* Empty Table */
.empty-message {
    text-align: center;
    color: #6c757d;
    padding: 25px;
}

/* Responsive */
@media (max-width: 1000px) {
    .sidebar {
        width: 220px;
        padding: 25px 15px;
    }

    .main {
        margin-left: 220px;
        padding: 25px 18px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 20px 12px;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
    }

    .table-wrapper {
        padding: 12px;
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
    <a href="portfolio.php">Portfolio</a>
    <a href="faq.php" class="active">FAQ</a>
    <a href="contact.php">Contact</a>

    <a href="../index.php" target="_blank">
        View Website
    </a>

</div>

<!-- Main -->
<div class="main">

    <div class="header">

        <div>
            <h1>Manage FAQ</h1>

            <p>
                Manage frequently asked questions on your website.
            </p>
        </div>

        <a href="add-faq.php" class="btn btn-add">
            + Add FAQ
        </a>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>No.</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (empty($faqs)): ?>

                <tr>
                    <td colspan="5" class="empty-message">
                        No FAQs found.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($faqs as $index => $faq): ?>

                    <tr>

                        <td><?= $index + 1 ?></td>

                        <td>
                            <?= e($faq["question"]) ?>
                        </td>

                        <td>
                            <?= e($faq["answer"]) ?>
                        </td>

                        <td>

                            <?php if ((int)$faq["status"] === 1): ?>

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

                                <!-- Edit FAQ -->
                                <a
                                    href="edit-faq.php?id=<?= (int)$faq["id"] ?>"
                                    class="btn btn-edit"
                                >
                                    Edit
                                </a>

                                <!-- Toggle FAQ -->
                                <form
                                    action="toggle-faq.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$faq["id"] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($_SESSION["csrf_token"]) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-status"
                                    >
                                        <?= (int)$faq["status"] === 1
                                            ? "Deactivate"
                                            : "Activate" ?>
                                    </button>

                                </form>

                                <!-- Delete FAQ -->
                                <form
                                    action="delete-faq.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$faq["id"] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($_SESSION["csrf_token"]) ?>"
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
