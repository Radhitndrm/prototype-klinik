<?php

require '../database/connection.php';

/**
 * Fungsi untuk login
 * Mengembalikan role user jika berhasil login, false jika gagal
 */
function login($email, $password)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session umum
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // Jika user adalah mentor, ambil nama divisinya langsung
        if ($user['role'] === 'mentor') {
            $stmtDivisi = $pdo->prepare("
                SELECT d.nama_divisi 
                FROM mentor_divisi md
                JOIN divisi d ON md.id_divisi = d.id_divisi
                WHERE md.id_user = ?
            ");
            $stmtDivisi->execute([$user['id_user']]);
            $divisiNama = $stmtDivisi->fetchAll(PDO::FETCH_COLUMN);

            // Simpan array nama divisi ke dalam session
            $_SESSION['divisi_mentor'] = $divisiNama;
        }

        return $user['role'];
    }

    return false;
}


/**
 * Mengecek apakah user sudah login
 * Jika belum, redirect ke halaman login
 */
function cekLogin()
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Fungsi untuk registrasi user baru
 * @return true jika sukses, string pesan error jika gagal
 */
function register($name, $email, $password, $confirmPassword)
{
    global $pdo;

    // Cek apakah email sudah terdaftar
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        return "Email sudah terdaftar!";
    }

    // Cek konfirmasi password
    if ($password !== $confirmPassword) {
        return "Password dan konfirmasi tidak cocok!";
    }

    $idUser = generateUserId();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Simpan data user baru
    $stmt = $pdo->prepare("INSERT INTO users (id_user, nama, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$idUser, $name, $email, $hashedPassword, 'anggota']);

    if ($success) {
        return true;
    } else {
        return "Registrasi gagal. Silahkan coba lagi.";
    }
}

/**
 * Fungsi untuk generate ID user secara otomatis
 * Format: USR-001, USR-002, dst
 */
function generateUserId()
{
    global $pdo;
    $stmt = $pdo->query("SELECT id_user FROM users ORDER BY id_user DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();

    if ($lastId) {
        $lastNumber = (int) str_replace('USR-', '', $lastId);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    return 'USR-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}
