<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "db_tmc";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", "$user", "$password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal" . $e->getMessage());
}
