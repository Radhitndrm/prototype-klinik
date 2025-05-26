<?php
require '../../database/connection.php';

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

    // Tidak perlu update role di sini, karena sudah ditangani oleh trigger

    $pdo->commit();

    echo "Mentor berhasil dihapus. Role akan otomatis diubah menjadi anggota jika tidak lagi menjadi mentor di divisi lain.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Gagal menghapus mentor: " . $e->getMessage();
}
