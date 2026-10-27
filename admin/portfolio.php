
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

if (empty($_SESSION["portfolio_csrf"])) {
    $_SESSION["portfolio_csrf"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["portfolio_csrf"];

// Load portfolio projects
$sql = "SELECT * FROM portfolio_items ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Error loading portfolio projects.");
}

$projects = $result->fetch_all(MYSQLI_ASSOC);

// Escape output safely
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Prepare image URL for the admin page
function portfolioImagePath($path) {
    $path = trim((string)$path);

    if ($path === "") {
        return "";
    }

    // Allow external URLs and absolute paths
    if (
        preg_match('/^https?:\/\//i', $path) ||
        str_starts_with($path, "/") ||
        str_starts_with($path, "../")
    ) {
        return $path;
    }

    // Relative paths such as assets/images/banner.png
    return "../" . $path;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Portfolio | NexaTech Admin</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #334155;
        }

        .container {
            max-width: 1250px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 12px;
            color: #37517e;
            font-size: 30px;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
            font-size: 15px;
        }

        .count {
            margin: 25px 0 18px;
            color: #37517e;
            font-size: 20px;
            font-weight: bold;
        }

        .add-btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 7px;
            background: #47b2e4;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .add-btn:hover {
            background: #299bd0;
        }

        .table-card {
            overflow-x: auto;
            background: white;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
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

        .project-image {
            width: 90px;
            height: 58px;
            object-fit: cover;
            border-radius: 6px;
            display: block;
            background: #eef3f9;
        }

        .no-image {
            color: #94a3b8;
            font-size: 12px;
        }

        .category {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background: #e8f3fb;
            color: #47769b;
            font-size: 12px;
        }

        .status {
            font-weight: bold;
        }

        .active {
            color: #25805a;
        }

        .inactive {
            color: #d97706;
        }

        .actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
        }

        .btn {
            display: inline-block;
            padding: 9px 12px;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
            font-family: inherit;
        }

        .edit-btn {
            background: #1769c2;
        }

        .toggle-btn {
            background: #e7a05b;
        }

        .delete-btn {
            background: #d93650;
        }

        .edit-btn:hover {
            background: #10549e;
        }

        .toggle-btn:hover {
            background: #cf8740;
        }

        .delete-btn:hover {
            background: #b91c38;
        }

        .action-form {
            display: inline;
            margin: 0;
        }

        .empty {
            padding: 35px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 650px) {
            body {
                padding: 18px 12px;
            }

            h1 {
                font-size: 25px;
            }

            .table-card {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <div>
            <h1>Manage Portfolio</h1>
            <p class="subtitle">
                Manage your projects and portfolio images.
            </p>
        </div>

        <a href="add-portfolio.php" class="add-btn">
            + Add Project
        </a>
    </div>

    <div class="count">
        All Projects (<?= count($projects) ?>)
    </div>

    <div class="table-card">

        <?php if (empty($projects)): ?>

            <div class="empty">
                No portfolio projects found.
            </div>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php $number = 1; ?>

                <?php foreach ($projects as $project): ?>

                    <?php
                    $projectId = (int)$project["id"];
                    $isActive = (int)$project["status"] === 1;
                    $imagePath = portfolioImagePath(
                        $project["image"] ?? ""
                    );
                    ?>

                    <tr>
                        <!-- Display sequential numbers, not database IDs -->
                        <td><?= $number++ ?></td>

                        <td>
                            <?php if ($imagePath !== ""): ?>
                                <img
                                    class="project-image"
                                    src="<?= e($imagePath) ?>"
                                    alt="<?= e($project["title"]) ?>"
                                >
                            <?php else: ?>
                                <span class="no-image">
                                    No image
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <strong><?= e($project["title"]) ?></strong>
                        </td>

                        <td>
                            <span class="category">
                                <?= e($project["category"]) ?>
                            </span>
                        </td>

                        <td>
                            <span class="status <?= $isActive ? "active" : "inactive" ?>">
                                <?= $isActive ? "Active" : "Inactive" ?>
                            </span>
                        </td>

                        <td>
                            <div class="actions">

                                <a
                                    class="btn edit-btn"
                                    href="edit-portfolio.php?id=<?= $projectId ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    class="action-form"
                                    action="toggle-portfolio.php"
                                    method="POST"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $projectId ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($csrfToken) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn toggle-btn"
                                    >
                                        <?= $isActive ? "Deactivate" : "Activate" ?>
                                    </button>
                                </form>

                                <form
                                    class="action-form"
                                    action="delete-portfolio.php"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this project?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $projectId ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($csrfToken) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn delete-btn"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
