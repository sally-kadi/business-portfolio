
<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../includes/db.php";

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: services.php");
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

// Validate service ID
$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: services.php?error=invalid_id");
    exit;
}

// Toggle service status
$stmt = $conn->prepare(
    "UPDATE services
     SET status = CASE
         WHEN status = 1 THEN 0
         ELSE 1
     END
     WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    header("Location: services.php?updated=1");
    exit;
}

$stmt->close();

header("Location: services.php?error=toggle_failed");
exit;
