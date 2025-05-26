<?php
require '../../database/connection.php';
function updateMateri($pdo, $id_materi, $judul, $divisi_id, $file)
{
    if (!$judul || !$divisi_id) {
        throw new Exception("Judul dan divisi wajib diisi.");
    }

    // Ambil file lama
    $stmt = $pdo->prepare("SELECT file_url FROM materi WHERE id_materi = ?");
    $stmt->execute([$id_materi]);
    $old_file = $stmt->fetchColumn();

    $filepath_db = $old_file;

    if ($file && $file['error'] !== 4) { // hanya jika ada file baru
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mov', 'avi', 'mkv'];

        if (!in_array($ext, $allowed)) {
            throw new Exception("Ekstensi file tidak valid.");
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("Ukuran file maksimal 5MB.");
        }

        // Hapus file lama
        if ($old_file && file_exists("../../" . $old_file)) {
            unlink("../../" . $old_file);
        }

        $upload_dir = __DIR__ . '/../../uploads/materi/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = uniqid('materi_') . '.' . $ext;
        $filepath = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Gagal mengunggah file.");
        }

        $filepath_db = 'uploads/materi/' . $filename;
    }

    $stmt = $pdo->prepare("UPDATE materi SET judul = ?, file_url = ?, divisi_id = ? WHERE id_materi = ?");
    $stmt->execute([$judul, $filepath_db, $divisi_id, $id_materi]);

    return true;
}
