<?php
// functions/delete_user.php

require '../database/connection.php'; // $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_user']) || empty($_POST['id_user'])) {
        die("ID user tidak valid.");
    }

    $id_user = (int) $_POST['id_user'];

    try {
        $pdo->beginTransaction();

        // Hapus user, trigger DB akan otomatis hapus mentor jika ada
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);

        $pdo->commit();

        header("Location: ../views/dashboard/users.php?success=delete");
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Gagal menghapus user: " . $e->getMessage());
    }
} else {
    header("Location: ../views/dashboard/users.php");
    exit;
}
