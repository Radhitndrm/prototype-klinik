<?php

require '../../database/connection.php';

function updateKonten($pdo, $id_konten, $judul, $divisi_id, $mentor_id, $file)
{
    if (!$judul || !$divisi_id || !$mentor_id) {
        throw new Exception("Judul, divisi, dan mentor wajib diisi.");
    }

    // Ambil file lama dari database
    $stmt = $pdo->prepare("SELECT gambar_url FROM konten_divisi WHERE id_konten = ?");
    $stmt->execute([$id_konten]);
    $old_file = $stmt->fetchColumn();

    $filepath_db = $old_file;

    // Jika ada file baru di-upload
    if ($file && $file['error'] !== 4) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new Exception("File harus berupa gambar (jpg, jpeg, png, gif) atau video (mp4, webm, ogg).");
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            throw new Exception("Ukuran file maksimal 10MB.");
        }

        // Hapus file lama jika ada dan masih eksis
        if ($old_file && file_exists(__DIR__ . '/../../' . $old_file)) {
            unlink(__DIR__ . '/../../' . $old_file);
        }

        // Pastikan direktori upload ada
        $upload_dir = rtrim(__DIR__ . '/../../uploads/konten', '/');
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Buat nama file baru yang unik
        $new_filename = uniqid('konten_') . '.' . $ext;
        $target_path = $upload_dir . '/' . $new_filename;

        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            throw new Exception("Gagal mengunggah file.");
        }

        // Path relatif untuk database
        $filepath_db =  $new_filename;
    }

    // Update data ke database
    $stmt = $pdo->prepare("UPDATE konten_divisi SET judul = ?, gambar_url = ?, divisi_id = ?, mentor_id = ? WHERE id_konten = ?");
    $stmt->execute([$judul, $filepath_db, $divisi_id, $mentor_id, $id_konten]);

    return true;
}
