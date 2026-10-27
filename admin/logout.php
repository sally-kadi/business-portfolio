
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

if (
    empty($_SESSION["logout_csrf"]) ||
    !hash_equals(
        $_SESSION["logout_csrf"],
        $_POST["csrf_token"] ?? ""
    )
) {
    http_response_code(403);
    exit("Invalid logout request.");
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 42000,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => $params["samesite"] ?: "Lax"
    ]);
}

session_destroy();


header("Location: login.php");
exit;
