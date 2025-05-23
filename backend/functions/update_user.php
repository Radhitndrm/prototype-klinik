<?php
require '../database/connection.php';

function generateMentorDivisiId($pdo)
{
    $lastId = $pdo->query("SELECT id_mentor_divisi FROM mentor_divisi ORDER BY id_mentor_divisi DESC LIMIT 1")->fetchColumn();
    $num = $lastId ? (int)substr($lastId, 4) + 1 : 1;
    return 'MDV-' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Metode request tidak valid.");
}

$id_user = $_POST['id_user'] ?? null;
$nama = $_POST['nama'] ?? null;
$email = $_POST['email'] ?? null;
$role = $_POST['role'] ?? null;
$divisi_id = $_POST['id_divisi'] ?? null;

if (!$id_user || !$nama || !$email || !$role) {
    die("Data tidak lengkap.");
}

try {
    $pdo->beginTransaction();

    // Update users
    $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id_user = ?");
    $stmt->execute([$nama, $email, $role, $id_user]);

    if ($role === 'mentor' && $divisi_id) {
        // Cek apakah relasi mentor_divisi sudah ada
        $exists = $pdo->prepare("SELECT COUNT(*) FROM mentor_divisi WHERE id_user = ?");
        $exists->execute([$id_user]);
        if ($exists->fetchColumn()) {
            // Update divisi
            $update = $pdo->prepare("UPDATE mentor_divisi SET id_divisi = ? WHERE id_user = ?");
            $update->execute([$divisi_id, $id_user]);
        } else {
            // Insert relasi baru
            $idMentorDivisi = generateMentorDivisiId($pdo);
            $insert = $pdo->prepare("INSERT INTO mentor_divisi (id_mentor_divisi, id_user, id_divisi) VALUES (?, ?, ?)");
            $insert->execute([$idMentorDivisi, $id_user, $divisi_id]);
        }
    } else {
        // Hapus relasi mentor_divisi kalau bukan mentor atau tidak pilih divisi
        $delete = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
        $delete->execute([$id_user]);
    }

    $pdo->commit();

    header("Location: ../views/dashboard/users.php?success-user=1");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal update data: " . $e->getMessage());
}
