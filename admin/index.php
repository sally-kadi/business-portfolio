
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Logout CSRF token
if (empty($_SESSION["logout_csrf"])) {
    $_SESSION["logout_csrf"] = bin2hex(random_bytes(32));
}

// Escape HTML output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Get total records from database
function getTotal($conn, $table) {

    $allowedTables = [
        "sections",
        "services",
        "portfolio_items",
        "faqs"
    ];

    if (!in_array($table, $allowedTables, true)) {
        return 0;
    }

    $result = $conn->query(
        "SELECT COUNT(*) AS total FROM `$table`"
    );

    if (!$result) {
        return 0;
    }

    $row = $result->fetch_assoc();

    return (int)$row["total"];
}

// Dynamic dashboard counts
$totalSections = getTotal($conn, "sections");
$totalServices = getTotal($conn, "services");
$totalPortfolio = getTotal($conn, "portfolio_items");
$totalFAQs = getTotal($conn, "faqs");

// Admin username
$username = e(
    $_SESSION["admin_username"] ?? "Admin"
);

// Logout token
$logoutToken = e($_SESSION["logout_csrf"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>NexaTech | Admin Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #26364d;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #37517e;
            padding: 30px 15px;
            overflow-y: auto;
        }

        .brand {
            text-align: center;
            margin-bottom: 38px;
        }

        .brand h2 {
            color: white;
            font-size: 25px;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .brand p {
            color: #b9d7eb;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .menu-label {
            color: #b9d7eb;
            font-size: 12px;
            letter-spacing: 2px;
            margin: 25px 14px 14px;
        }

        .sidebar a {
            display: block;
            padding: 15px 17px;
            margin-bottom: 8px;
            border-radius: 9px;
            color: white;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.25s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #47b2e4;
        }

        .logout-form {
            margin-top: 10px;
        }

        .logout-btn {
            width: 100%;
            padding: 15px 17px;
            background: #e74c3c;
            border: none;
            border-radius: 9px;
            color: white;
            text-align: left;
            font-size: 15px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            padding: 38px;
            min-height: 100vh;
        }

        /* Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 38px;
        }

        .top-header h1 {
            color: #37517e;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .top-header p {
            color: #748096;
            font-size: 15px;
            line-height: 1.6;
        }

        .admin-badge {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            color: #37517e;
            font-size: 14px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
        }

        /* Section Titles */
        .section-title {
            font-size: 22px;
            color: #26364d;
            margin-bottom: 25px;
        }

        /* Overview Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 42px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 15px;
            border-left: 4px solid #47b2e4;
            box-shadow: 0 5px 18px rgba(0,0,0,0.04);
            transition: 0.25s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card h3 {
            color: #52637b;
            font-size: 15px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .stat-number {
            color: #37517e;
            font-size: 36px;
            font-weight: bold;
        }

        /* Quick Access */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .quick-card {
            display: block;
            background: white;
            border-radius: 15px;
            padding: 27px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 5px 18px rgba(0,0,0,0.04);
            transition: 0.25s;
        }

        .quick-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(55,81,126,0.12);
        }

        .quick-card h3 {
            color: #37517e;
            font-size: 17px;
            margin-bottom: 14px;
        }

        .quick-card p {
            color: #748096;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            margin-top: 55px;
            color: #9aa5b5;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
                padding: 25px 18px;
            }

            .top-header h1 {
                font-size: 27px;
            }

            .stats-grid,
            .quick-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">

    <div class="brand">
        <h2>NexaTech</h2>
        <p>Admin Panel</p>
    </div>

    <div class="menu-label">MAIN MENU</div>

    <a href="index.php" class="active">
        ◈ Dashboard
    </a>

    <a href="sections.php">
        ▦ Sections
    </a>

    <a href="services.php">
        ◈ Services
    </a>

    <a href="portfolio.php">
        ▣ Portfolio
    </a>

    <a href="faq.php">
        ◉ FAQ
    </a>

    <a href="contact.php">
        ✉ Contact
    </a>

    <div class="menu-label">OTHER</div>

    <a
        href="../index.php"
        target="_blank"
        rel="noopener noreferrer"
    >
        ↗ View Website
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

    <!-- Header -->
    <div class="top-header">

        <div>
            <h1>Dashboard</h1>

            <p>
                Welcome, <?= $username ?>!
                Manage your NexaTech website.
            </p>
        </div>

        <div class="admin-badge">
            ◈ NexaTech Admin Panel
        </div>

    </div>

    <!-- Overview -->
    <h2 class="section-title">Overview</h2>

    <div class="stats-grid">

        <div class="stat-card">
            <h3>Total Sections</h3>

            <div class="stat-number">
                <?= $totalSections ?>
            </div>
        </div>

        <div class="stat-card">
            <h3>Total Services</h3>

            <div class="stat-number">
                <?= $totalServices ?>
            </div>
        </div>

        <div class="stat-card">
            <h3>Total Portfolio Projects</h3>

            <div class="stat-number">
                <?= $totalPortfolio ?>
            </div>
        </div>

        <div class="stat-card">
            <h3>Total FAQs</h3>

            <div class="stat-number">
                <?= $totalFAQs ?>
            </div>
        </div>

    </div>

    <!-- Quick Access -->
    <h2 class="section-title">Quick Access</h2>

    <div class="quick-grid">

        <a href="sections.php" class="quick-card">
            <h3>▦ Manage Sections</h3>

            <p>
                Update website sections, titles,
                subtitles and content.
            </p>
        </a>

        <a href="services.php" class="quick-card">
            <h3>◈ Manage Services</h3>

            <p>
                Add, edit, activate or deactivate
                your business services.
            </p>
        </a>

        <a href="portfolio.php" class="quick-card">
            <h3>▣ Manage Portfolio</h3>

            <p>
                Add projects, update images
                and manage portfolio status.
            </p>
        </a>

        <a href="faq.php" class="quick-card">
            <h3>◉ Manage FAQ</h3>

            <p>
                Add and edit frequently asked
                questions.
            </p>
        </a>

        <a href="contact.php" class="quick-card">
            <h3>✉ Manage Contact</h3>

            <p>
                Update your company name,
                email, phone number and address.
            </p>
        </a>

        <a
            href="../index.php"
            target="_blank"
            rel="noopener noreferrer"
            class="quick-card"
        >
            <h3>↗ View Website</h3>

            <p>
                Preview the public NexaTech
                business portfolio website.
            </p>
        </a>

    </div>

    <!-- Footer -->
    <div class="footer">
        NexaTech Admin Panel &copy; <?= date("Y") ?>
    </div>

</div>

</body>
</html>
