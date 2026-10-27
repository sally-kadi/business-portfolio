
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Prevent browser caching of protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// CSRF token for logout
if (empty($_SESSION["logout_csrf"])) {
    $_SESSION["logout_csrf"] = bin2hex(random_bytes(32));
}

// Get total sections
$sections = $conn->query(
    "SELECT COUNT(*) AS total FROM sections"
);

// Get total services
$services = $conn->query(
    "SELECT COUNT(*) AS total FROM services"
);

$totalSections = $sections
    ? (int) $sections->fetch_assoc()["total"]
    : 0;

$totalServices = $services
    ? (int) $services->fetch_assoc()["total"]
    : 0;

$username = htmlspecialchars(
    $_SESSION["admin_username"] ?? "Admin",
    ENT_QUOTES,
    "UTF-8"
);

$logoutToken = htmlspecialchars(
    $_SESSION["logout_csrf"],
    ENT_QUOTES,
    "UTF-8"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>NexaTech | Admin Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #37517e;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
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
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            padding: 40px;
        }

        .header {
            margin-bottom: 35px;
        }

        .header h1 {
            color: #37517e;
            font-size: 30px;
            margin-bottom: 10px;
        }

        .header p {
            color: #777;
            line-height: 1.6;
        }

        /* Statistics Cards */
        .cards {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(230px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.06);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #37517e;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .card p {
            font-size: 38px;
            font-weight: bold;
            color: #47b2e4;
        }

        .card span {
            display: block;
            margin-top: 12px;
            color: #888;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            color: #999;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main {
                margin-left: 0;
                padding: 25px;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">

    <h2>NexaTech Admin</h2>

    <a href="index.php" class="active">
        Dashboard
    </a>

    <a href="sections.php">
        Sections
    </a>

    <a href="services.php">
        Services
    </a>

    <a href="portfolio.php">
        Portfolio
    </a>

    <a href="faq.php">
        FAQ
    </a>

    <a href="contact.php">
        Contact
    </a>

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
            value="<?= $logoutToken ?>"
        >

        <button
            type="submit"
            class="logout-btn"
        >
            Logout
        </button>
    </form>

</div>

<!-- Main Content -->
<div class="main">

    <div class="header">

        <h1>NexaTech Admin Dashboard</h1>

        <p>
            Welcome, <?= $username ?>!
            Manage your website content easily.
        </p>

    </div>

    <!-- Statistics Cards -->
    <div class="cards">

        <div class="card">

            <h3>Total Sections</h3>

            <p><?= $totalSections ?></p>

            <span>Website Sections</span>

        </div>

        <div class="card">

            <h3>Total Services</h3>

            <p><?= $totalServices ?></p>

            <span>Available Services</span>

        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        NexaTech Admin Panel
        &copy; <?= date("Y") ?>
    </div>

</div>

</body>
</html>
