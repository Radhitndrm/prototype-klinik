<?php
require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode request tidak valid.");
}

$id_user = $_POST['id_user'] ?? null;

if (!$id_user) {
    die("ID user tidak ditemukan.");
}

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // Hapus relasi mentor_divisi
    $stmt = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
    $stmt->execute([$id_user]);

    // Update role user menjadi anggota
    $stmt = $pdo->prepare("UPDATE users SET role = 'anggota' WHERE id_user = ?");
    $stmt->execute([$id_user]);

    $pdo->commit();

    echo "Mentor berhasil dihapus dan diubah menjadi anggota.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Gagal menghapus mentor: " . $e->getMessage();
}
