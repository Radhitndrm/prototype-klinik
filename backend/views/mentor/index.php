<?php
session_start();
require '../../database/connection.php';
require '../../functions/mentor/fetch.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mentor') {
    header("Location: ../../login.php");
    exit;
}

// Ambil data divisi_mentor dari session yang ternyata array, ambil elemen pertamanya (nama divisi)
$divisi_mentor = $_SESSION['divisi_mentor'];
$divisi_mentor_nama = is_array($divisi_mentor) ? $divisi_mentor[0] : $divisi_mentor;

$id_user = $_SESSION['id_user'];

$nama_mentor = fetchColumnById($pdo, "SELECT nama FROM users WHERE id_user = ?", $id_user);

$nama_divisi = $divisi_mentor_nama;
$stmt = $pdo->prepare("SELECT id_divisi FROM divisi WHERE nama_divisi = ?");
$stmt->execute([$divisi_mentor_nama]);
$id_divisi = $stmt->fetchColumn();

try {
    // Total materi untuk divisi mentor yang sedang login
    $stmtMateri = $pdo->prepare("
        SELECT COUNT(*) AS total_materi
        FROM materi
        WHERE divisi_id = ?
    ");
    $stmtMateri->execute([$id_divisi]);
    $total_materi = $stmtMateri->fetch(PDO::FETCH_ASSOC)['total_materi'];

    // Total konten untuk divisi mentor yang sedang login
    $stmtKonten = $pdo->prepare("
        SELECT COUNT(*) AS total_konten
        FROM konten_divisi
        WHERE divisi_id = ?
    ");
    $stmtKonten->execute([$id_divisi]);
    $total_konten = $stmtKonten->fetch(PDO::FETCH_ASSOC)['total_konten'];
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Mentor - Klinik Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans text-gray-800">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 fixed inset-y-0 left-0 flex flex-col justify-between border-r border-gray-700 shadow-lg">
            <div>
                <div class="px-6 py-8">
                    <h1 class="text-3xl font-bold text-gray-200 mb-10">Klinik Prodi</h1>
                    <nav class="flex flex-col space-y-3 text-gray-300">
                        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 12l2-2 4 4 8-8 2 2v6H3z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="materi.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
                            </svg>
                            Material Management
                        </a>
                        <a href="content.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            Content Management
                        </a>
                    </nav>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-700">
                <form action="../../functions/logout.php" method="post">
                    <button type="submit" class="w-full px-4 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main content wrapper -->
        <div class="flex-1 ml-64 flex flex-col">

            <!-- Header -->
            <header class="flex justify-between items-center bg-purple-900 shadow-md p-4 sticky top-0 z-10">
                <div class="text-xl font-semibold text-white">Dashboard Mentor</div>
                <div class="flex items-center space-x-4">
                    <input type="search" placeholder="Search..."
                        class="px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 border border-purple-700 bg-purple-800 text-white placeholder-purple-300" />
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <div
                            class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center font-bold text-white select-none">
                            <?= htmlspecialchars(substr($nama_mentor, 0, 1)) ?>
                        </div>
                        <div class="text-white font-medium"><?= htmlspecialchars($nama_mentor) ?></div>
                    </div>
                </div>
            </header>

            <!-- Main dashboard content -->
            <main class="p-6 overflow-y-auto flex-1 bg-gray-100">

                <!-- Welcome banner -->
                <section class="mb-8">
                    <h2 class="text-3xl font-bold text-purple-700 mb-1">Selamat Datang, <?= htmlspecialchars($nama_mentor) ?></h2>
                    <p class="text-gray-600"><?= htmlspecialchars($nama_divisi) ?></p>
                </section>

                <!-- Statistik Cards -->
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div
                        class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center border-l-4 border-purple-600 hover:shadow-lg transition cursor-pointer">
                        <h3 class="text-lg font-semibold text-purple-700 mb-2">Total Materi</h3>
                        <p class="text-4xl font-extrabold text-gray-900"><?= htmlspecialchars($total_materi) ?></p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center border-l-4 border-purple-600 hover:shadow-lg transition cursor-pointer">
                        <h3 class="text-lg font-semibold text-purple-700 mb-2">Total Konten</h3>
                        <p class="text-4xl font-extrabold text-gray-900"><?= htmlspecialchars($total_konten) ?></p>
                    </div>
                </section>


            </main>
        </div>
    </div>

</body>

</html>