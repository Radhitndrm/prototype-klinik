<?php
session_start();
require '../../database/connection.php';


if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login_page.php");
    exit();
}

// Total pengguna
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_users = $row['total'];
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}

// Jumlah materi per divisi
try {
    $materi_per_divisi = $pdo->query("
        SELECT d.nama_divisi, COUNT(m.id_materi) AS jumlah_materi
        FROM divisi d
        LEFT JOIN materi m ON d.id_divisi = m.divisi_id
        GROUP BY d.id_divisi
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query materi gagal: " . $e->getMessage());
}

// Jumlah konten per divisi
try {
    $konten_per_divisi = $pdo->query("
        SELECT d.nama_divisi, COUNT(k.id_konten) AS jumlah_konten
        FROM divisi d
        LEFT JOIN konten_divisi k ON d.id_divisi = k.divisi_id
        GROUP BY d.id_divisi
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query konten gagal: " . $e->getMessage());
}

// 5 komentar terbaru
try {
    $komentar_terbaru = $pdo->query("
        SELECT u.nama, k.isi_komentar, kd.judul, k.tanggal_komentar
        FROM komentar k
        JOIN users u ON k.id_user = u.id_user
        JOIN konten_divisi kd ON k.id_konten = kd.id_konten
        ORDER BY k.tanggal_komentar DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query komentar gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin - Klinik Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans text-gray-800">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside
            class="w-64 bg-gray-800 fixed inset-y-0 left-0 flex flex-col justify-between border-r border-gray-700 shadow-lg">
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
                        <a href="divisi.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94" />
                            </svg>
                            Divisi Management
                        </a>
                        <a href="users.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path
                                    d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                            </svg>
                            Users Management
                        </a>
                        <a href="#reports"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 12h18M9 6h6M9 18h6" />
                            </svg>
                            Reports
                        </a>
                        <a href="#settings"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3" />
                                <path
                                    d="M19.4 15a1.77 1.77 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.77 1.77 0 0 0-1.82-.33 1.77 1.77 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.77 1.77 0 0 0-1-1.51 1.77 1.77 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.77 1.77 0 0 0 .33-1.82 1.77 1.77 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.77 1.77 0 0 0 1.51-1 1.77 1.77 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.77 1.77 0 0 0 1.82.33H9a1.77 1.77 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.77 1.77 0 0 0 1 1.51 1.77 1.77 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.77 1.77 0 0 0-.33 1.82V9a1.77 1.77 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.77 1.77 0 0 0-1.51 1z" />
                            </svg>
                            Settings
                        </a>
                    </nav>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-700">
                <button
                    class="w-full px-4 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition"><a href="../../functions/logout.php"
                        class="block text-center w-full px-4 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                        Logout
                    </a></button>
            </div>
        </aside>

        <!-- Main content wrapper -->
        <div class="flex-1 ml-64 flex flex-col">

            <!-- Header -->
            <header class="flex justify-between items-center bg-purple-900 shadow-md p-4 sticky top-0 z-10">
                <div class="text-xl font-semibold text-white">Dashboard Admin</div>
                <div class="flex items-center space-x-4">
                    <input type="search" placeholder="Search..."
                        class="px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 border border-purple-700 bg-purple-800 text-white placeholder-purple-300" />
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <div
                            class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center font-bold text-white select-none">A
                        </div>
                        <div class="text-white font-medium">Admin</div>
                    </div>
                </div>
            </header>

            <!-- Main dashboard content -->
            <main class="p-6 overflow-y-auto flex-1 bg-gray-100">

                <!-- Welcome banner -->
                <section class="mb-8">
                    <h2 class="text-3xl font-bold text-purple-700 mb-1">Selamat Datang, Admin!</h2>
                    <p class="text-gray-600">Kelola semua data dan aktivitas Klinik Prodi dengan mudah di dashboard ini.</p>
                </section>

                <!-- Statistik Cards -->
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div
                        class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center border-l-4 border-purple-600 hover:shadow-lg transition cursor-pointer">
                        <h3 class="text-lg font-semibold text-purple-700 mb-2">Total Divisi</h3>
                        <p class="text-4xl font-extrabold text-gray-900">7</p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center border-l-4 border-purple-600 hover:shadow-lg transition cursor-pointer">
                        <h3 class="text-lg font-semibold text-purple-700 mb-2">Total Pengguna</h3>
                        <p class="text-4xl font-extrabold text-gray-900"><?php echo $total_users ?></p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center justify-center border-l-4 border-purple-600 hover:shadow-lg transition cursor-pointer">
                        <h3 class="text-lg font-semibold text-purple-700 mb-2">Laporan Bulanan</h3>
                        <p class="text-4xl font-extrabold text-gray-900">23</p>
                    </div>
                </section>
                <section class="mb-8">
                    <h3 class="text-xl font-semibold text-purple-700 mb-4">Jumlah Konten per Divisi</h3>
                    <ul class="space-y-2">
                        <?php foreach ($konten_per_divisi as $row): ?>
                            <li class="bg-white p-4 rounded shadow border-l-4 border-indigo-500">
                                <?= htmlspecialchars($row['nama_divisi']) ?>: <strong><?= $row['jumlah_konten'] ?></strong> konten
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <section class="mb-8">
                    <h3 class="text-xl font-semibold text-purple-700 mb-4">Jumlah Materi per Divisi</h3>
                    <ul class="space-y-2">
                        <?php foreach ($materi_per_divisi as $row): ?>
                            <li class="bg-white p-4 rounded shadow border-l-4 border-purple-500">
                                <?= htmlspecialchars($row['nama_divisi']) ?>: <strong><?= $row['jumlah_materi'] ?></strong> materi
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <section class="mb-8">
                    <h3 class="text-xl font-semibold text-purple-700 mb-4">5 Komentar Terbaru</h3>
                    <ul class="space-y-3">
                        <?php foreach ($komentar_terbaru as $komentar): ?>
                            <li class="bg-white p-4 rounded shadow border-l-4 border-gray-400">
                                <div class="text-sm text-gray-600">
                                    <strong><?= htmlspecialchars($komentar['nama']) ?></strong> di konten <em><?= htmlspecialchars($komentar['judul']) ?></em>:
                                </div>
                                <div class="text-gray-800 mt-1"><?= htmlspecialchars($komentar['isi_komentar']) ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?= date('d M Y, H:i', strtotime($komentar['tanggal_komentar'])) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>



                <!-- Divisi Management -->
                <section id="divisi" class="mb-10">
                    <h3
                        class="text-2xl font-semibold text-purple-700 border-b-4 border-purple-700 inline-block pb-1 mb-6">Divisi Management
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div
                            class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition cursor-pointer border border-gray-200">
                            <h4 class="font-semibold text-purple-700 mb-3">Divisi Teknologi</h4>
                            <p class="text-gray-700">Melayani kebutuhan IT dan pengembangan aplikasi.</p>
                        </div>
                        <div
                            class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition cursor-pointer border border-gray-200">
                            <h4 class="font-semibold text-purple-700 mb-3">Divisi Pendidikan</h4>
                            <p class="text-gray-700">Mengelola materi pembelajaran dan pelatihan.</p>
                        </div>
                        <div
                            class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition cursor-pointer border border-gray-200">
                            <h4 class="font-semibold text-purple-700 mb-3">Divisi Komunikasi</h4>
                            <p class="text-gray-700">Menangani hubungan eksternal dan promosi.</p>
                        </div>
                    </div>
                </section>

                <!-- Users Management -->
                <section id="users" class="mb-10">
                    <h3
                        class="text-2xl font-semibold text-purple-700 border-b-4 border-purple-700 inline-block pb-1 mb-6">Users Management
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-gray-300 shadow-sm bg-white">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200">
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200">
                                        Nama Pengguna
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-200">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Role
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-purple-50 cursor-pointer">
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">1</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">Ahmad Fauzi</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">ahmad@example.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Admin</td>
                                </tr>
                                <tr class="hover:bg-purple-50 cursor-pointer">
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">2</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">Siti Nurhaliza</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">siti@example.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap">User</td>
                                </tr>
                                <tr class="hover:bg-purple-50 cursor-pointer">
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">3</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">Budi Santoso</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">budi@example.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap">User</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Settings -->
                <section id="settings" class="mb-10">
                    <h3
                        class="text-2xl font-semibold text-purple-700 border-b-4 border-purple-700 inline-block pb-1 mb-6">Pengaturan
                    </h3>
                    <p class="text-gray-700">Kelola pengaturan akun dan aplikasi di sini.</p>
                    <button
                        class="mt-4 px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg shadow hover:from-purple-700 hover:to-indigo-700 transition font-semibold">
                        Ubah Pengaturan
                    </button>
                </section>

            </main>

        </div>

    </div>

</body>

</html>