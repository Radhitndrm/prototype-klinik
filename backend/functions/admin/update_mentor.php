<?php
require '../../database/connection.php';

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
$id_divisi = $_POST['divisi_id'] ?? null;  // divisi_id sebagai string, bukan array

if (!$id_user || !$id_divisi) {
    die("Data tidak lengkap.");
}

// Karena divisi_id bukan array, tidak perlu count
// Namun jika kamu ingin validasi tambahan, bisa cek apakah id_divisi valid
// Misal cek format id_divisi atau cek di database apakah id_divisi ada

try {
    $pdo->beginTransaction();

    // Hapus relasi lama mentor
    $stmtDelete = $pdo->prepare("DELETE FROM mentor_divisi WHERE id_user = ?");
    $stmtDelete->execute([$id_user]);

    // Insert relasi baru
    $stmtInsert = $pdo->prepare("INSERT INTO mentor_divisi (id_mentor_divisi, id_user, id_divisi) VALUES (?, ?, ?)");
    $newId = generateMentorDivisiId($pdo);
    $stmtInsert->execute([$newId, $id_user, $id_divisi]);

    $pdo->commit();

    header("Location: ../../views/admin/users.php?success-mentor=1");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Gagal update mentor: " . $e->getMessage());
}
