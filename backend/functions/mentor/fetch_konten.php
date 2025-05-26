<?php
// Mengambil semua konten milik mentor di divisi tertentu
function fetchKonten(PDO $pdo, int $divisi_id, int $mentor_id): array
{
    $sql = "SELECT * FROM konten_divisi WHERE divisi_id = ? AND mentor_id = ? ORDER BY tanggal_upload DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$divisi_id, $mentor_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
