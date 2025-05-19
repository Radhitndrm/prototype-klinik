<?php
session_start();
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = login($_POST['email'], $_POST['password']);
    if ($role) {
        if ($role == 'anggota') {
            header("Location: ../../frontend/main/index.html");
        } else {
            echo "anda adalah seorang sepuh";
            // header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>

</html>