<?php
require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';

    if (!$id_user) {
        die("ID user tidak ditemukan.");
    }

    try {
        $pdo->beginTransaction();

        // Cek role user
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $role = $stmt->fetchColumn();

        if (!$role) {
            throw new Exception('User tidak ditemukan.');
        }

        if ($role === 'mentor') {
            // Hapus data mentor divisi terlebih dahulu
            $stmt = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
            $stmt->execute([$id_user]);
        }

        // Hapus user dari tabel users
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);

        $pdo->commit();

        echo "Pengguna berhasil dihapus.";
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo  $e->getMessage();
        exit;
    }
} else {
    header("Location: ../views/dashboard/users.php?status=error&message=" . urlencode("Metode request tidak valid."));
    exit;
}
