<?php
require '../../database/connection.php';

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
$divisi_ids = $_POST['divisi_ids'] ?? [];

if (!$id_user || empty($divisi_ids)) {
    die("Data tidak lengkap.");
}

try {
    $pdo->beginTransaction();

    // Update role user jadi mentor
    $stmt = $pdo->prepare("UPDATE users SET role = 'mentor' WHERE id_user = ?");
    $stmt->execute([$id_user]);

    // Hapus dulu relasi mentor_divisi lama kalau ada
    $delete = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
    $delete->execute([$id_user]);

    // Insert relasi baru untuk tiap divisi yang dipilih
    $insert = $pdo->prepare("INSERT INTO mentor_divisi (id_mentor_divisi, id_user, id_divisi) VALUES (?, ?, ?)");

    foreach ($divisi_ids as $divisi_id) {
        $idMentorDivisi = generateMentorDivisiId($pdo);
        $insert->execute([$idMentorDivisi, $id_user, $divisi_id]);
    }

    $pdo->commit();

    header("Location: ../../views/admin/users.php?success-add-mentor=1");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal menambah mentor: " . $e->getMessage());
}
