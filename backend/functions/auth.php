<?php
require_once __DIR__ . '/../database/connection.php';

function login($email, $password)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
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

function register($name, $email, $password, $confirmPassword)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM  users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        return "Email sudah terdaftar!";
    }

    if ($password !== $confirmPassword) {
        return "Password dan konfirmasi tidak cocok!";
    }
    $idUser = generateUserId();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (id_user,nama, email, password, role) VALUES (?,?,?,?,?)");
    $success = $stmt->execute([$idUser, $name, $email, $hashedPassword, 'anggota']);
    if ($success) {
        return true;
    } else {
        return "Registrasi gagal. Silahkan coba lagi.";
    }
}

function generateUserId()
{
    global $pdo;
    $stmt = $pdo->query("SELECT id_user FROM users ORDER BY id_user DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();

    if ($lastId) {
        $lastNumber = (int) str_replace('USR-', '', $lastId);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    return 'USR-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}
