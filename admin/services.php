
<?php
require_once __DIR__ . '/../includes/db.php';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if ($title !== '' && $description !== '') {
        $stmt = $conn->prepare(
            "INSERT INTO services (title, description, status, sort_order)
             VALUES (?, ?, 1, ?)"
        );

        $stmt->bind_param("ssi", $title, $description, $sortOrder);

        if ($stmt->execute()) {
            header('Location: services.php?success=1');
            exit;
        } else {
            $message = 'Unable to add the service. Please try again.';
        }

        $stmt->close();
    } else {
        $message = 'Please enter both the title and description.';
    }
}

if (isset($_GET['success'])) {
    $message = 'Service added successfully!';
}

$result = $conn->query(
    "SELECT id, title, description, status, sort_order
     FROM services
     ORDER BY sort_order ASC, id ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | Violet Programming</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            background: #f7f8fc;
        }

        .admin-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 25px;
        }

        .admin-heading {
            color: #24356e;
            margin-bottom: 25px;
        }

        .admin-panel {
            background: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(31, 45, 85, .07);
        }

        .admin-panel h2 {
            color: #24356e;
            margin-bottom: 20px;
        }

        .admin-form label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }

        .admin-form input,
        .admin-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d9dce7;
            border-radius: 6px;
            font: inherit;
        }

        .admin-form textarea {
            min-height: 100px;
            resize: vertical;
        }

        .admin-form button {
            margin-top: 18px;
            border: none;
            cursor: pointer;
            font: inherit;
        }

        .message {
            padding: 12px;
            background: #eee8ff;
            color: #49318a;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #ececf2;
        }

        th {
            color: #24356e;
            background: #f7f8fc;
        }

        .status-active {
            color: #16803c;
            font-weight: bold;
        }

        .status-inactive {
            color: #b42318;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #7952d8;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header class="navbar">
    <a href="../index.php" class="logo">
        Violet<span>Programming</span>
    </a>

    <nav>
        <a href="../index.php">View Website</a>
        <a href="services.php">Manage Services</a>
    </nav>
</header>

<main class="admin-container">
    <a class="back-link" href="../index.php">← Back to Website</a>

    <h1 class="admin-heading">Admin Panel — Services</h1>

    <section class="admin-panel">
        <h2>Add New Service</h2>

        <?php if ($message !== ''): ?>
            <p class="message"><?= e($message) ?></p>
        <?php endif; ?>

        <form class="admin-form" method="POST" action="services.php">
            <label for="title">Service Title</label>
            <input
                type="text"
                id="title"
                name="title"
                required
                maxlength="255"
                placeholder="Example: Web Development"
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                required
                placeholder="Describe the service..."
            ></textarea>

            <label for="sort_order">Display Order</label>
            <input
                type="number"
                id="sort_order"
                name="sort_order"
                min="0"
                value="5"
                required
            >

            <button class="btn" type="submit">Add Service</button>
        </form>
    </section>

    <section class="admin-panel">
        <h2>Existing Services</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($service = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= (int)$service['id'] ?></td>
                                <td><?= e($service['title']) ?></td>
                                <td><?= e($service['description']) ?></td>
                                <td><?= (int)$service['sort_order'] ?></td>
                                <td>
                                    <?php if ((int)$service['status'] === 1): ?>
                                        <span class="status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No services found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>
