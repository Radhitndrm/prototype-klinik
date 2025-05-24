<?php
require '../database/connection.php';

function generateMentorDivisiId($pdo)
{
    $lastId = $pdo->query("SELECT id_mentor_divisi FROM mentor_divisi ORDER BY id_mentor_divisi DESC LIMIT 1")->fetchColumn();
    $num = $lastId ? (int)substr($lastId, 4) + 1 : 1;
    return 'MDV-' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Request tidak valid.");
}

$id_user = $_POST['id_user'] ?? null;
$divisi_ids = $_POST['divisi_ids'] ?? [];

if (!$id_user || !is_array($divisi_ids)) {
    die("Data tidak lengkap.");
}

try {
    $pdo->beginTransaction();

    // Hapus relasi lama
    $stmtDelete = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
    $stmtDelete->execute([$id_user]);

    // Insert relasi baru dengan id_mentor_divisi generated
    $stmtInsert = $pdo->prepare("INSERT INTO mentor_divisi (id_mentor_divisi, id_user, id_divisi) VALUES (?, ?, ?)");

    foreach ($divisi_ids as $id_divisi) {
        $newId = generateMentorDivisiId($pdo);
        $stmtInsert->execute([$newId, $id_user, $id_divisi]);
    }

    $pdo->commit();

    header("Location: ../views/dashboard/users.php?success-mentor=1");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Gagal update mentor: " . $e->getMessage());
}
