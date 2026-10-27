
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Load all sections
$result = $conn->query(
    "SELECT id, section_key, title, subtitle, status
     FROM sections
     ORDER BY id ASC"
);

if (!$result) {
    die("Database error: " . $conn->error);
}

$sections = $result->fetch_all(MYSQLI_ASSOC);

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// CSRF token for delete forms
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Sections | NexaTech Admin</title>

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
    margin: 0 0 30px;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.header-actions h2 {
    color: #37517e;
    margin: 0;
}

.btn {
    display: inline-block;
    padding: 10px 15px;
    border: none;
    border-radius: 7px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
}

.btn-add {
    background: #47b2e4;
    color: white;
}

.btn-edit {
    background: #37517e;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.table-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 16px 12px;
    text-align: left;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
}

th {
    background: #f8f9fc;
    color: #37517e;
    font-size: 14px;
}

td {
    font-size: 14px;
}

.section-name {
    font-weight: 600;
    color: #37517e;
    text-transform: capitalize;
}

.section-title {
    font-weight: 500;
    color: #333;
    min-width: 150px;
}

.subtitle-text {
    color: #777;
    max-width: 240px;
    overflow-wrap: anywhere;
}

.status-active {
    color: #198754;
    font-weight: bold;
}

.status-inactive {
    color: #dc3545;
    font-weight: bold;
}

.actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: nowrap;
    white-space: nowrap;
}

.actions form {
    margin: 0;
}

.empty {
    text-align: center;
    padding: 30px;
    color: #777;
}

@media (max-width: 1100px) {
    .actions {
        flex-wrap: wrap;
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
        padding: 20px;
    }

    .header-actions {
        flex-direction: column;
        align-items: flex-start;
    }

    .table-card {
        padding: 12px;
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

    <a href="../index.php" target="_blank">
        View Website
    </a>
</div>

<div class="main">

    <h1>Manage Sections</h1>

    <div class="header-actions">
        <h2>
            All Sections (<?= count($sections) ?>)
        </h2>

        <a href="add-section.php" class="btn btn-add">
            + Add Section
        </a>
    </div>

    <div class="table-card">

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Section</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (empty($sections)): ?>

                <tr>
                    <td colspan="6" class="empty">
                        No sections found.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($sections as $section): ?>

                    <tr>
                        <td>
                            <?= (int)$section["id"] ?>
                        </td>

                        <td class="section-name">
                            <?= e($section["section_key"]) ?>
                        </td>

                        <td class="section-title">
                            <?= e($section["title"]) ?>
                        </td>

                        <td class="subtitle-text">
                            <?= e($section["subtitle"]) ?>
                        </td>

                        <td>
                            <?php if ((int)$section["status"] === 1): ?>
                                <span class="status-active">
                                    Active
                                </span>
                            <?php else: ?>
                                <span class="status-inactive">
                                    Inactive
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="actions">

                                <a
                                    href="edit-section.php?id=<?= (int)$section['id'] ?>"
                                    class="btn btn-edit"
                                >
                                    Edit
                                </a>

                                <?php
                                $isCoreSection = in_array(
                                    $section["section_key"],
                                    ["home", "about", "services", "contact"],
                                    true
                                );
                                ?>

                                <?php if (!$isCoreSection): ?>

                                    <form
                                        action="delete-section.php"
                                        method="POST"
                                        onsubmit="return confirm('Delete this section?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$section['id'] ?>"
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

                                <?php endif; ?>

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
