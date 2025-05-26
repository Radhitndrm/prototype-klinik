<?php
require '../../database/connection.php';
function deleteMateri(PDO $pdo, string $id_materi, string $uploadDir = '../../uploads/'): bool
{
    try {
        // Ambil nama file video dari database berdasarkan id_materi
        $stmt = $pdo->prepare("SELECT file_url FROM materi WHERE id_materi = ?");
        $stmt->execute([$id_materi]);
        $file_url = $stmt->fetchColumn();

        if ($file_url) {
            // Dapatkan path file yang sebenarnya, gunakan basename untuk keamanan path traversal
            $filePath = $uploadDir . basename($file_url);
            if (file_exists($filePath)) {
                unlink($filePath); // hapus file video dari server
            }
        }

        // Hapus data materi dari database
        $stmt = $pdo->prepare("DELETE FROM materi WHERE id_materi = ?");
        $stmt->execute([$id_materi]);

        return true;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}
