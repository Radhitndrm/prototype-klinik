<?php
// update_user.php

require '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $nama = $_POST['nama'] ?? null;
    $email = $_POST['email'] ?? null;
    $role = $_POST['role'] ?? null;
    $divisi_id = $_POST['divisi_id'] ?? null;

    if (!$id_user || !$nama || !$email || !$role) {
        die("Data tidak lengkap.");
    }

    try {
        $pdo->beginTransaction();

        // Update data users
        $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id_user = ?");
        $stmt->execute([$nama, $email, $role, $id_user]);

        if ($role === 'mentor') {
            if ($divisi_id) {
                // Cek apakah sudah ada relasi mentor_divisi
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM mentor_divisi WHERE id_user = ?");
                $stmtCheck->execute([$id_user]);
                $exists = $stmtCheck->fetchColumn();

                if ($exists) {
                    // Update divisi mentor
                    $stmtUpdateDivisi = $pdo->prepare("UPDATE mentor_divisi SET id_divisi = ? WHERE id_user = ?");
                    $stmtUpdateDivisi->execute([$divisi_id, $id_user]);
                } else {
                    // Insert relasi mentor_divisi baru
                    $stmtInsertDivisi = $pdo->prepare("INSERT INTO mentor_divisi (id_user, id_divisi) VALUES (?, ?)");
                    $stmtInsertDivisi->execute([$id_user, $divisi_id]);
                }
            } else {
                // Jika mentor tapi tidak pilih divisi, hapus relasi kalau ada
                $stmtDeleteDivisi = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
                $stmtDeleteDivisi->execute([$id_user]);
            }
        } else {
            // Jika bukan mentor, pastikan hapus relasi mentor_divisi
            $stmtDeleteDivisi = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
            $stmtDeleteDivisi->execute([$id_user]);
        }

        $pdo->commit();

        // Redirect kembali ke users.php dengan pesan sukses
        header("Location: ../pages/users.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Gagal update data: " . $e->getMessage());
    }
} else {
    die("Metode request tidak valid.");
}
