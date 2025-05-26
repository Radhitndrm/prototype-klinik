<?php
require '../../database/connection.php';

function generateMateriId($pdo)
{
    $lastId = $pdo->query("SELECT id_materi FROM materi ORDER BY id_materi DESC LIMIT 1")->fetchColumn();
    $num = $lastId ? (int)substr($lastId, 4) + 1 : 1;
    return 'MTR-' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

function createMateri($pdo, $judul, $divisi_id, $file)
{
    if (!$judul || !$divisi_id || !$file) {
        throw new Exception("Semua field wajib diisi.");
    }

    if ($file['error'] !== 0) {
        throw new Exception("Terjadi kesalahan saat mengunggah file.");
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mov', 'avi', 'mkv']; // jika ingin mengizinkan video juga

    if (!in_array($ext, $allowed)) {
        throw new Exception("Ekstensi file tidak valid. Hanya PDF, DOC, DOCX, PPT, PPTX, atau video yang diizinkan.");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("Ukuran file maksimal 5MB.");
    }

    $id_materi = generateMateriId($pdo);

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

    $stmt = $pdo->prepare("INSERT INTO materi (id_materi, judul, file_url, divisi_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_materi, $judul, $filepath_db, $divisi_id]);

    return true;
}
