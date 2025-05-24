<?php
require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode request tidak valid.");
}

$id_user = $_POST['id_user'] ?? null;
$nama = $_POST['nama'] ?? null;
$email = $_POST['email'] ?? null;

if (!$id_user || !$nama || !$email) {
    die("Data tidak lengkap.");
}

try {
    $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ? WHERE id_user = ?");
    $stmt->execute([$nama, $email, $id_user]);

    header("Location: ../views/dashboard/users.php?success-user=1");
    exit();
} catch (Exception $e) {
    die("Gagal update data: " . $e->getMessage());
}
