
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Allow POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: faq.php");
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

// Validate FAQ ID
$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: faq.php?error=invalid_id");
    exit;
}

// Check if FAQ exists
$stmt = $conn->prepare(
    "SELECT id FROM faqs WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$faq = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$faq) {
    header("Location: faq.php?error=not_found");
    exit;
}

// Delete FAQ
$stmt = $conn->prepare(
    "DELETE FROM faqs WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    header("Location: faq.php?deleted=1");
    exit;
}

$stmt->close();

header("Location: faq.php?error=delete_failed");
exit;
