
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (empty($_SESSION["logout_csrf"])) {
    $_SESSION["logout_csrf"] = bin2hex(random_bytes(32));
}

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$result = $conn->query(
    "SELECT id, title, description, image, status, sort_order
     FROM services
     ORDER BY sort_order ASC, id ASC"
);

if (!$result) {
    die("Error loading services.");
}

$services = $result->fetch_all(MYSQLI_ASSOC);

$csrfToken = $_SESSION["csrf_token"];
$logoutToken = $_SESSION["logout_csrf"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Services | NexaTech Admin</title>

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
            color: white;
            text-decoration: none;
            padding: 14px 18px;
            margin-bottom: 12px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #47b2e4;
        }

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
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .main {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
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

        .btn {
            display: inline-block;
            padding: 9px 12px;
            border: none;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            font-size: 12px;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-add {
            padding: 12px 18px;
            background: #47b2e4;
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

        .table-wrapper {
            background: white;
            border-radius: 12px;
            padding: 18px;
            overflow-x: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            min-width: 760px;
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
            font-size: 13px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #fafcff;
        }

        .description {
            max-width: 350px;
            line-height: 1.6;
            color: #64748b;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
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

        .actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
        }

        .actions form {
            display: inline;
            margin: 0;
        }

        .empty {
            padding: 35px;
            text-align: center;
            color: #64748b;
        }

        .footer {
            margin-top: 50px;
            color: #999;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                padding: 25px;
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
                <h1>Manage Services</h1>
                <p class="subtitle">
                    Manage your website services dynamically.
                </p>
            </div>

            <a href="add-service.php" class="btn btn-add">
                + Add Service
            </a>

        </div>

        <div class="count">
            All Services (<?= count($services) ?>)
        </div>

        <div class="table-wrapper">

            <table>
                <thead>
                    <tr>
                        <th>No.</th>
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

                    <?php $number = 1; ?>

                    <?php foreach ($services as $service): ?>

                        <?php
                        $serviceId = (int)$service["id"];
                        $isActive = (int)$service["status"] === 1;
                        ?>

                        <tr>
                            <td><?= $number++ ?></td>

                            <td>
                                <strong>
                                    <?= e($service["title"]) ?>
                                </strong>
                            </td>

                            <td class="description">
                                <?= e($service["description"]) ?>
                            </td>

                            <td>
                                <?= (int)$service["sort_order"] ?>
                            </td>

                            <td>
                                <span class="status <?= $isActive ? 'active-status' : 'inactive-status' ?>">
                                    <?= $isActive ? "Active" : "Inactive" ?>
                                </span>
                            </td>

                            <td>
                                <div class="actions">

                                    <a
                                        href="edit-service.php?id=<?= $serviceId ?>"
                                        class="btn btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="toggle-service.php"
                                        method="POST"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $serviceId ?>"
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

                                    <form
                                        action="delete-service.php"
                                        method="POST"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $serviceId ?>"
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
