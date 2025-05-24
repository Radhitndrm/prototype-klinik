<?php
require '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode request tidak valid.");
}

$id_user = $_POST['id_user'] ?? null;
$nama = $_POST['nama'] ?? null;
$email = $_POST['email'] ?? null;
$role = $_POST['role'] ?? null;

if (!$id_user || !$nama || !$email || !$role) {
    die("Data tidak lengkap.");
}

try {
    // Update users (nama, email, role)
    $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id_user = ?");
    $stmt->execute([$nama, $email, $role, $id_user]);

    // **Tidak ada kode mentor_divisi di sini**

    header("Location: ../../views/admin/users.php?success-user=1");
    exit();
} catch (Exception $e) {
    die("Gagal update data: " . $e->getMessage());
}
