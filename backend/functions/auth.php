<?php
require_once __DIR__ . '/../database/connection.php';

function login($email, $password)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === $password) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        return $user['role'];
    }

    return false;
}

function cekLogin()
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit;
    }
}
