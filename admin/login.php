
<?php
session_start();
require_once __DIR__ . "/../includes/db.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

if (empty($_SESSION["login_csrf"])) {
    $_SESSION["login_csrf"] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["login_csrf"], $token)) {
        $error = "Invalid request. Please refresh and try again.";
    } elseif ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM admin_users
             WHERE username = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $dbUsername, $hash);

        $valid = false;

        if ($stmt->fetch()) {
            $valid = password_verify($password, $hash);
        }

        $stmt->close();

        if ($valid) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $id;
            $_SESSION["admin_username"] = $dbUsername;

            header("Location: index.php");
            exit;
        }

        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | NexaTech</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #37517e, #263b61);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-box {
    background: #fff;
    width: 400px;
    max-width: 100%;
    padding: 40px;
    border-radius: 14px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.18);
}

.logo {
    color: #37517e;
    font-size: 29px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 8px;
}

.subtitle {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
}

label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

input {
    width: 100%;
    padding: 13px;
    margin-bottom: 20px;
    border: 1px solid #d6dce5;
    border-radius: 8px;
    font-size: 15px;
}

input:focus {
    outline: none;
    border-color: #47b2e4;
}

button {
    width: 100%;
    padding: 14px;
    background: #47b2e4;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #299bd0;
}

.error {
    background: #ffe8e8;
    color: #b42318;
    padding: 12px;
    border-radius: 7px;
    margin-bottom: 20px;
    font-size: 14px;
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 24px;
    color: #37517e;
    text-decoration: none;
    font-size: 14px;
}
</style>
</head>

<body>

<div class="login-box">

    <div class="logo">NexaTech</div>
    <p class="subtitle">Admin Panel Login</p>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION["login_csrf"], ENT_QUOTES, "UTF-8") ?>"
        >

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter username"
            autocomplete="username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Login</button>
    </form>

    <a href="../index.php" class="back-link">
        ← Back to Website
    </a>

</div>

</body>
</html>
