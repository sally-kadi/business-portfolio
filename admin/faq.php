
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// CSRF tokens
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (empty($_SESSION["logout_csrf"])) {
    $_SESSION["logout_csrf"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["csrf_token"];
$logoutToken = $_SESSION["logout_csrf"];

// Escape HTML safely
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #334155;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #37517e;
            color: white;
            padding: 30px 20px;
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 40px;
            text-align: center;
        }

        .sidebar a {
            display: block;
            padding: 14px 18px;
            margin-bottom: 12px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #47b2e4;
        }

        /* Logout */
        .logout-form {
            margin-top: 22px;
        }

        .logout-btn {
            display: block;
            width: 100%;
            padding: 14px 18px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            text-align: left;
            cursor: pointer;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Main content */
        .main {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
            min-width: 0;
        }

        .container {
            max-width: 1250px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 25px;
        }

        h1 {
            color: #37517e;
            font-size: 30px;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #64748b;
            font-size: 15px;
        }

        .count {
            margin: 25px 0 18px;
            color: #37517e;
            font-size: 20px;
            font-weight: bold;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 9px 12px;
            border: none;
            border-radius: 6px;
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
            padding: 12px 18px;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-edit {
            background: #1769c2;
        }

        .btn-status {
            background: #e7a05b;
        }

        .btn-delete {
            background: #d93650;
        }

        .btn:hover {
            opacity: 0.85;
        }

        /* Table */
        .table-wrapper {
            background: white;
            border-radius: 12px;
            padding: 18px;
            overflow-x: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
        }

        th {
            padding: 16px 12px;
            background: #f1f5f9;
            color: #37517e;
            text-align: left;
            font-size: 13px;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #edf0f5;
            vertical-align: middle;
            font-size: 13px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        tbody tr:hover {
            background: #fafcff;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .question {
            font-weight: bold;
            color: #37517e;
            min-width: 180px;
        }

        .answer {
            color: #64748b;
            min-width: 250px;
        }

        /* Status */
        .status {
            display: inline-block;
            min-width: 78px;
            padding: 5px 10px;
            border-radius: 20px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
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
            flex-wrap: wrap;
            gap: 7px;
        }

        .actions form {
            display: inline-flex;
            margin: 0;
            flex-shrink: 0;
        }

        .empty-message {
            text-align: center;
            color: #64748b;
            padding: 35px;
        }

        .footer {
            margin-top: 50px;
            color: #999;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                padding: 25px 15px;
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

    <!-- Secure Logout -->
    <form
        class="logout-form"
        action="logout.php"
        method="POST"
    >
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($logoutToken) ?>"
        >

        <button type="submit" class="logout-btn">
            Logout
        </button>
    </form>

</div>

<!-- Main Content -->
<div class="main">
    <div class="container">

        <div class="header">
            <div>
                <h1>Manage FAQ</h1>
                <p class="subtitle">
                    Manage frequently asked questions on your website.
                </p>
            </div>

            <a href="add-faq.php" class="btn btn-add">
                + Add FAQ
            </a>
        </div>

        <div class="count">
            All FAQs (<?= count($faqs) ?>)
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

                        <?php
                        $faqId = (int)$faq["id"];
                        $isActive = (int)$faq["status"] === 1;
                        ?>

                        <tr>
                            <!-- Sequential number -->
                            <td><?= $index + 1 ?></td>

                            <td class="question">
                                <?= e($faq["question"]) ?>
                            </td>

                            <td class="answer">
                                <?= e($faq["answer"]) ?>
                            </td>

                            <td>
                                <span class="status <?= $isActive ? 'active-status' : 'inactive-status' ?>">
                                    <?= $isActive ? "Active" : "Inactive" ?>
                                </span>
                            </td>

                            <td>
                                <div class="actions">

                                    <!-- Edit -->
                                    <a
                                        href="edit-faq.php?id=<?= $faqId ?>"
                                        class="btn btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <!-- Activate / Deactivate -->
                                    <form
                                        action="toggle-faq.php"
                                        method="POST"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $faqId ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($csrfToken) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-status"
                                        >
                                            <?= $isActive ? "Deactivate" : "Activate" ?>
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form
                                        action="delete-faq.php"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this FAQ?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $faqId ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($csrfToken) ?>"
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

        <div class="footer">
            NexaTech Admin Panel &copy; <?= date("Y") ?>
        </div>

    </div>
</div>

</body>
</html>
