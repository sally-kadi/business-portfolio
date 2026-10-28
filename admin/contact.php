
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Prevent caching
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

// Escape HTML output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$error = "";
$success = "";

// Load existing company information
$result = $conn->query(
    "SELECT id, company_name, email, phone, address
     FROM company_info
     ORDER BY id ASC
     LIMIT 1"
);

if (!$result) {
    exit("Unable to load company information.");
}

$company = $result->fetch_assoc();

if (!$company) {
    $company = [
        "id" => null,
        "company_name" => "",
        "email" => "",
        "phone" => "",
        "address" => ""
    ];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page.");
    }

    $companyNameInput = $_POST["company_name"] ?? "";
    $emailInput = $_POST["email"] ?? "";
    $phoneInput = $_POST["phone"] ?? "";
    $addressInput = $_POST["address"] ?? "";

    if (
        !is_string($companyNameInput) ||
        !is_string($emailInput) ||
        !is_string($phoneInput) ||
        !is_string($addressInput)
    ) {
        http_response_code(400);
        exit("Invalid form data.");
    }

    $companyName = trim($companyNameInput);
    $email = trim($emailInput);
    $phone = trim($phoneInput);
    $address = trim($addressInput);

    // Validate fields
    if (
        $companyName === "" ||
        $email === "" ||
        $phone === "" ||
        $address === ""
    ) {
        $error = "Please fill in all fields.";

    } elseif (strlen($companyName) > 150) {
        $error = "Company name is too long.";

    } elseif (
        strlen($email) > 255 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $error = "Please enter a valid email address.";

    } elseif (strlen($phone) > 50) {
        $error = "Phone number is too long.";

    } else {

        if ($company["id"] !== null) {

            $stmt = $conn->prepare(
                "UPDATE company_info
                 SET company_name = ?,
                     email = ?,
                     phone = ?,
                     address = ?
                 WHERE id = ?"
            );

            $id = (int)$company["id"];

            $stmt->bind_param(
                "ssssi",
                $companyName,
                $email,
                $phone,
                $address,
                $id
            );

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO company_info
                    (company_name, email, phone, address)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $companyName,
                $email,
                $phone,
                $address
            );
        }

        if ($stmt->execute()) {
            $stmt->close();

            header("Location: contact.php?updated=1");
            exit;

        } else {
            $error = "Unable to save company information.";
            $stmt->close();
        }
    }

    // Keep submitted values if validation fails
    $company["company_name"] = $companyName;
    $company["email"] = $email;
    $company["phone"] = $phone;
    $company["address"] = $address;
}

// Show success message
if (
    $_SERVER["REQUEST_METHOD"] === "GET" &&
    ($_GET["updated"] ?? "") === "1"
) {
    $success = "Company information updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Contact Management | NexaTech Admin</title>

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
    font-size: 24px;
}

.sidebar a {
    display: block;
    padding: 14px 18px;
    margin-bottom: 10px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.3s;
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
}

.logout-btn:hover {
    background: #c0392b;
}

/* Main */
.main {
    margin-left: 250px;
    padding: 40px;
    min-height: 100vh;
}

.page-header {
    margin-bottom: 28px;
}

.page-header h1 {
    color: #37517e;
    font-size: 30px;
    margin-bottom: 10px;
}

.page-header p {
    color: #6c757d;
    font-size: 15px;
}

/* Form Card */
.form-card {
    background: white;
    max-width: 850px;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.form-card h2 {
    color: #37517e;
    font-size: 20px;
    margin-bottom: 8px;
}

.form-intro {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 28px;
}

/* Form Fields */
.form-group {
    margin-bottom: 22px;
}

.form-group label {
    display: block;
    margin-bottom: 9px;
    font-size: 14px;
    font-weight: bold;
    color: #37517e;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 13px 15px;
    border: 1px solid #dce3ed;
    border-radius: 8px;
    background: white;
    font-family: Arial, sans-serif;
    font-size: 15px;
    color: #333;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #47b2e4;
    box-shadow: 0 0 0 3px rgba(71, 178, 228, 0.12);
}

.form-group textarea {
    min-height: 125px;
    resize: vertical;
}

/* Buttons */
.form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: 28px;
}

.btn {
    display: inline-block;
    padding: 12px 22px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-family: Arial, sans-serif;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}

.btn-save {
    background: #47b2e4;
    color: white;
}

.btn-save:hover {
    background: #279acb;
}

.btn-view {
    background: #37517e;
    color: white;
}

.btn-view:hover {
    background: #263e66;
}

/* Alerts */
.success,
.error {
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 22px;
    font-size: 14px;
}

.success {
    background: #d1e7dd;
    color: #0f5132;
}

.error {
    background: #f8d7da;
    color: #842029;
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
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 20px;
    }

    .form-card {
        padding: 22px;
    }

    .page-header h1 {
        font-size: 25px;
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
    <a href="faq.php">FAQ</a>

    <a href="contact.php" class="active">
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
            value="<?= e($_SESSION["logout_csrf"]) ?>"
        >

        <button type="submit" class="logout-btn">
            Logout
        </button>
    </form>

</div>

<!-- Main Content -->
<div class="main">

    <div class="page-header">
        <h1>Contact Management</h1>
        <p>
            Manage your company contact information dynamically.
        </p>
    </div>

    <div class="form-card">

        <h2>Company Information</h2>

        <p class="form-intro">
            Update the details displayed in the Contact section
            of your website.
        </p>

        <?php if ($success !== ""): ?>
            <div class="success" role="status">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="error" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Contact Form -->
        <form method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION["csrf_token"]) ?>"
            >

            <div class="form-group">
                <label for="company_name">
                    Company Name
                </label>

                <input
                    type="text"
                    id="company_name"
                    name="company_name"
                    value="<?= e($company["company_name"]) ?>"
                    maxlength="150"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($company["email"]) ?>"
                    maxlength="255"
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?= e($company["phone"]) ?>"
                    maxlength="50"
                    required
                >
            </div>

            <div class="form-group">
                <label for="address">
                    Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    required
                ><?= e($company["address"]) ?></textarea>
            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-save"
                >
                    Save Changes
                </button>

                <a
                    href="../index.php#contact"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-view"
                >
                    View Contact Section
                </a>

            </div>

        </form>

    </div>

    <div class="footer">
        NexaTech Admin Panel &copy; <?= date("Y") ?>
    </div>

</div>

</body>
</html>
