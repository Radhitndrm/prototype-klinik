<?php
// update_divisi.php

require '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_divisi = $_POST['id_divisi'] ?? '';
    $nama_divisi = $_POST['nama_divisi'] ?? '';
    $deskripsi = $_POST['desc_divisi'] ?? '';
    $gambar_lama = $_POST['gambar_lama'] ?? '';

    if ($id_divisi && $nama_divisi && $deskripsi) {
        // Folder upload gambar
        $uploadDir = __DIR__ . '/../../uploads/';

        // Cek apakah ada file gambar yang diupload
        if (isset($_FILES['gambar_divisi']) && $_FILES['gambar_divisi']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['gambar_divisi']['tmp_name'];
            $fileName = $_FILES['gambar_divisi']['name'];
            $fileSize = $_FILES['gambar_divisi']['size'];
            $fileType = $_FILES['gambar_divisi']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Valid extension
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Batas max file size (misal 2MB)
                $maxFileSize = 2 * 1024 * 1024;
                if ($fileSize <= $maxFileSize) {
                    // Generate nama file unik untuk mencegah overwrite
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                    // Pindahkan file ke folder uploads
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Hapus gambar lama jika ada dan bukan kosong
                        if ($gambar_lama && file_exists($uploadDir . $gambar_lama)) {
                            unlink($uploadDir . $gambar_lama);
                        }

                        // Update data termasuk gambar baru
                        $stmt = $pdo->prepare("UPDATE divisi SET nama_divisi = ?, desc_divisi = ?, gambar_divisi = ? WHERE id_divisi = ?");
                        $stmt->execute([$nama_divisi, $deskripsi, $newFileName, $id_divisi]);

                        header('Location: ../../views/admin/divisi.php?success=1');
                        exit;
                    } else {
                        die("Gagal memindahkan file gambar.");
                    }
                } else {
                    die("Ukuran file terlalu besar. Maksimum 2MB.");
                }
            } else {
                die("Format file tidak didukung. Hanya jpg, jpeg, png, gif, dan webp.");
            }
        } else {
            // Jika tidak upload gambar baru, update tanpa mengubah gambar
            $stmt = $pdo->prepare("UPDATE divisi SET nama_divisi = ?, desc_divisi = ? WHERE id_divisi = ?");
            $stmt->execute([$nama_divisi, $deskripsi, $id_divisi]);

            header('Location: ../../views/admin/divisi.php?success=1');
            exit;
        }
    } else {
        echo "<h3>Data tidak lengkap. Berikut data yang diterima:</h3>";
        echo "<pre>";
        var_dump([
            'id_divisi' => $id_divisi,
            'nama_divisi' => $nama_divisi,
            'desc_divisi' => $deskripsi,
            'gambar_lama' => $gambar_lama,
            'file_upload' => $_FILES['gambar_divisi'] ?? null
        ]);
        echo "</pre>";
        exit;
    }
} else {
    header('Location: ../../views/admin/divisi.php');
    exit;
}
