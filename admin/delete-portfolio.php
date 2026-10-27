
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Allow POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: portfolio.php");
    exit;
}

// Validate CSRF token
$token = $_POST["csrf_token"] ?? "";

if (
    empty($_SESSION["csrf_token"]) ||
    !is_string($token) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(403);
    exit("Invalid request. Please refresh the page.");
}

// Validate project ID
$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: portfolio.php?error=invalid_id");
    exit;
}

// Check if project exists
$stmt = $conn->prepare(
    "SELECT id FROM portfolio_items WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    header("Location: portfolio.php?error=not_found");
    exit;
}

// Delete project
$stmt = $conn->prepare(
    "DELETE FROM portfolio_items WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    header("Location: portfolio.php?deleted=1");
    exit;
}

$stmt->close();

header("Location: portfolio.php?error=delete_failed");
exit;
