<?php
require '../../database/connection.php';

function generateKontenId($pdo)
{
    $lastId = $pdo->query("SELECT id_konten FROM konten_divisi ORDER BY id_konten DESC LIMIT 1")->fetchColumn();
    $num = $lastId ? (int)substr($lastId, 4) + 1 : 1; // Ambil angka setelah "KON-"
    return 'KON-' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

function addKonten($pdo, $judul, $divisi_id, $mentor_id, $file)
{
    if (!$judul || !$divisi_id || !$mentor_id) {
        throw new Exception("Field judul, divisi, dan mentor wajib diisi.");
    }

    $id_konten = generateKontenId($pdo);

    $fileName = null;
    if ($file && $file['error'] === 0) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            throw new Exception("File harus berupa gambar (jpg, png, gif) atau video (mp4, webm, ogg).");
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            throw new Exception("Ukuran file maksimal 10MB.");
        }

        // Pastikan direktori upload ada
        $uploadDir = __DIR__ . '/../../uploads/konten';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Buat nama file unik
        $randomName = uniqid('konten_') . '.' . $ext;
        $targetPath = rtrim($uploadDir, '/') . '/' . $randomName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Gagal mengunggah file.");
        }

        // Simpan path relatif (untuk disimpan ke database dan digunakan oleh front-end)
        $fileName = $randomName;
    }

    // Simpan data ke database
    $stmt = $pdo->prepare("INSERT INTO konten_divisi (id_konten, judul, divisi_id, mentor_id, gambar_url, tanggal_upload)
                           VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$id_konten, $judul, $divisi_id, $mentor_id, $fileName]);

    return true;
}
