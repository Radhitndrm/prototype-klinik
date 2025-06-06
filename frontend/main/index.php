<?php
session_start();
require '../../backend/database/connection.php';
require '../../backend/functions/mentor/fetch.php';

// Ambil id_user dari session, pastikan ada
$id_user = $_SESSION['id_user'] ?? null;
$role = $_SESSION['role'] ?? null;  // sesuaikan key session-nya, kalau memang 'role'
$nama = $_SESSION['nama'] ?? null;

if ($id_user) {
    // Jika nama belum ada di session, ambil dari database
    if (!$nama) {
        $nama = fetchColumnById($pdo, "SELECT nama FROM users WHERE id_user = ?", $id_user);
        if (!$nama) {
            $nama = 'User';
        }
    }

    if (!$role) {
        $role = fetchColumnById($pdo, "SELECT role FROM users WHERE id_user = ?", $id_user);
        if (!$role) {
            $role = 'user';
        }
    }
} else {
    // Jika id_user tidak ada di session, beri nilai default
    $nama = 'Guest';
    $role = null;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Technology Media Center - Klinik Prodi</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out both;
        }

        .fade-delay-1 {
            animation-delay: 0.3s;
        }

        .fade-delay-2 {
            animation-delay: 0.6s;
        }

        .fade-delay-3 {
            animation-delay: 0.9s;
        }

        .fade-delay-4 {
            animation-delay: 1.2s;
        }

        /* scrollbar untuk overflow */
        .overflow-auto::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: #7c3aed;
            /* purple-700 */
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-[#fefcff] to-[#f1f5ff] text-gray-800 font-sans leading-relaxed">

    <div class="max-w-[1200px] mx-auto p-5">

        <!-- Navbar -->

        <nav
            class="flex justify-between items-center p-5 bg-white/80 backdrop-blur-sm rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] mb-10 sticky top-5 z-50 mx-auto max-w-screen-xl">
            <!-- Logo kiri -->
            <div>
                <img src="images/logo.png" alt="Logo Klinik" class="rounded-2xl shadow-md shadow-indigo-500/50 max-w-[200px]" />
            </div>

            <!-- Semua menu + dropdown di kanan -->
            <div class="flex items-center gap-8">

                <ul class="hidden md:flex gap-8 list-none">
                    <li><a href="#" class="text-gray-800 font-medium hover:text-purple-600 transition">Beranda</a></li>
                    <li><a href="#visi-misi" class="text-gray-800 font-medium hover:text-purple-600 transition">Visi & Misi</a></li>
                    <li><a href="#divisi" class="text-gray-800 font-medium hover:text-purple-600 transition">Divisi</a></li>
                    <li><a href="#kontak" class="text-gray-800 font-medium hover:text-purple-600 transition">Kontak</a></li>
                </ul>

                <!-- Dropdown user -->
                <div class="relative">
                    <button id="userBtn" class="text-gray-800 font-medium hover:text-purple-600 transition focus:outline-none flex items-center gap-1">
                        <?= htmlspecialchars($nama) ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <ul id="dropdownMenu"
                        class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                        <?php if ($role === 'admin'): ?>
                            <li><a href="/admin/dashboard.php" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Kembali ke Dashboard Admin</a></li>
                        <?php elseif ($role === 'mentor'): ?>
                            <li><a href="/mentor/dashboard.php" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Kembali ke Dashboard Mentor</a></li>
                        <?php elseif ($role === 'user'): ?>
                            <li><a href="/logout.php" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Logout</a></li>
                        <?php else: ?>
                            <li><a href="../../backend/views/login_page.php" class="block px-4 py-2 text-gray-800 hover:bg-purple-100">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </nav>
        <!-- Hero Section -->
        <header id="home"
            class="text-center p-20 rounded-3xl mb-12 shadow-lg bg-gradient-to-br from-[#e0f4ff] to-[#f3e8ff] fade-in fade-delay-1">
            <h1 class="text-4xl font-bold mb-3">
                Selamat Datang di <span class="text-purple-700">Technology Medical Center</span>
            </h1>
            <p class="text-lg text-gray-600 mb-8">
                Komunitas belajar interaktif untuk semua mahasiswa fakultas Teknologi & Sains!
            </p>
            <div class="flex flex-wrap justify-center gap-5 max-w-[900px] mx-auto">
                <a href="../Full-Stack/index.php" class="flex-1 min-w-[250px] max-w-[300px]">
                    <button
                        class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-md hover:from-purple-800 hover:to-indigo-700 transform hover:-translate-y-1 transition whitespace-nowrap">
                        Masuk Divisi Fullstack
                    </button>
                </a>
                <a href="../Jaringan/index.html" class="flex-1 min-w-[250px] max-w-[300px]">
                    <button
                        class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-md hover:from-purple-800 hover:to-indigo-700 transform hover:-translate-y-1 transition whitespace-nowrap">
                        Masuk Divisi Jaringan
                    </button>
                </a>
                <a href="../Kewirahusaan/index.html" class="flex-1 min-w-[250px] max-w-[300px]">
                    <button
                        class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-md hover:from-purple-800 hover:to-indigo-700 transform hover:-translate-y-1 transition whitespace-nowrap">
                        Masuk Divisi Kewirausahaan
                    </button>
                </a>
                <a href="../Pengabdian/index.html" class="flex-1 min-w-[250px] max-w-[300px]">
                    <button
                        class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-md hover:from-purple-800 hover:to-indigo-700 transform hover:-translate-y-1 transition">
                        Masuk Divisi Pengabdian Masyarakat
                    </button>
                </a>
                <a href="../K3L/index.html" class="flex-1 min-w-[250px] max-w-[300px]">
                    <button
                        class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-md hover:from-purple-800 hover:to-indigo-700 transform hover:-translate-y-1 transition">
                        Masuk Divisi Kesehatan, Keselamatan, dan Lingkungan
                    </button>
                </a>
                <a href="../Kreativitas/index.html" class="flex-1 min-w-[250px] max-w-[300px]">
                    <button
                        class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-md hover:from-purple-800 hover:to-indigo-700 transform hover:-translate-y-1 transition whitespace-nowrap">
                        Masuk Divisi Kreativitas
                    </button>
                </a>
            </div>

        </header>

        <!-- Visi & Misi -->
        <section id="visi-misi" class="bg-gray-100 rounded-2xl p-8 mb-12 fade-in fade-delay-2">
            <h2 class=" text-center text-3xl mb-8 font-semibold">🎯 Visi & Misi TMC</h2>
            <div class="grid md:grid-cols-2 gap-6 max-h-[500px] overflow-auto pr-2">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-purple-700 text-center mb-4 border-b-4 border-purple-700 pb-2 font-semibold">Visi
                    </h3>
                    <p>
                        Pada tahun 2037, menjadi pusat pengembangan dan kolaborasi sains dan teknologi lintas prodi yang
                        unggul, inovatif, dan islami, berfokus pada penyelesaian permasalahan sosial-lingkungan berbasis
                        teknologi informasi, serta memperkuat jejaring dan kontribusi nyata bagi masyarakat.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md overflow-auto max-h-[300px]">
                    <h3 class="text-purple-700 text-center mb-4 border-b-4 border-purple-700 pb-2 font-semibold">Misi
                    </h3>
                    <ol class="list-decimal list-inside space-y-2 text-justify">
                        <li>Menyelenggarakan layanan pendidikan, konsultasi, dan pengembangan keilmuan lintas prodi
                            secara islami dan berbasis teknologi informasi untuk mendukung peningkatan kompetensi
                            mahasiswa di bidang sains dan teknologi.</li>
                        <li>Mengembangkan riset kolaboratif dan inovasi terapan yang berorientasi pada solusi
                            permasalahan sosial-lingkungan dan mendukung pembangunan berkelanjutan di tingkat lokal,
                            nasional, dan internasional.</li>
                        <li>Mengintegrasikan keahlian dari Teknik Informatika, Teknik Mesin, Teknik Geologi, dan Teknik
                            Sipil dalam program pengabdian masyarakat serta pemberdayaan berbasis teknologi tepat guna
                            dan kearifan lokal.</li>
                        <li>Membangun jejaring strategis dengan institusi, industri, asosiasi profesi, dan komunitas,
                            baik di dalam maupun luar negeri, untuk memperkuat daya saing, pertukaran pengetahuan, dan
                            pengembangan karir mahasiswa.</li>
                        <li>Menanamkan nilai-nilai keislaman, profesionalisme, dan kewirausahaan dalam setiap aktivitas
                            klinik prodi guna menghasilkan lulusan yang berkarakter, inovatif, dan adaptif terhadap
                            tantangan zaman.</li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- Divisi lengkap dengan gambar -->
        <section id="divisi" class="mb-12 fade-in fade-delay-3>

            <div class=" bg-white rounded-3xl font-semibold mb-8 p-6">
            <h2 class="text-center text-3xl font-semibold mb-8">💡 List Divisi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div
                    class="bg-white rounded-xl shadow-md p-0 text-center flex flex-col overflow-hidden transform transition-transform duration-300 hover:-translate-y-2">
                    <div class="h-44 w-full overflow-hidden">
                        <img src="images/fullstack-developer.png" alt="Divisi Fullstack"
                            class="object-cover w-full h-full" />
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold mb-2 text-purple-700">Divisi Fullstack</h3>
                        <p class="mb-6 flex-grow">Fokus pada pengembangan aplikasi web dan software dengan teknologi
                            terbaru.</p>
                        <a href="../Full-Stack/index.html"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:from-purple-700 hover:to-indigo-700 hover:scale-[1.03] transition-transform duration-200 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Masuk Divisi
                        </a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-md p-0 text-center flex flex-col overflow overflow-hidden transform transition-transform duration-300 hover:-translate-y-2 shadow-md ">
                    <div class="h-44 w-full overflow-hidden">
                        <img src="images/jaringan.jpeg" alt="Divisi Jaringan"
                            class="object-cover mx-auto h-44 w-full" />
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold mb-2 text-purple-700">Divisi Jaringan</h3>
                        <p class="mb-6 flex-grow">Pelajari konfigurasi jaringan, keamanan, dan manajemen
                            infrastruktur
                            jaringan.</p>
                        <a href="../Jaringan/index.html"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:from-purple-700 hover:to-indigo-700 hover:scale-[1.03] transition-transform duration-200 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Masuk Divisi
                        </a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-md p-0 text-center flex flex-col overflow-hidden transform transition-transform duration-300 hover:-translate-y-2 shadow-md">
                    <div class="h-44 w-full overflow-hidden">
                        <img src="images/wirausaha.jpg" alt="Divisi Pengabdian Masyarakat"
                            class="object-cover w-full h-full" />
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold mb-2 text-purple-700">Divisi Kewirahusaan</h3>
                        <p class="mb-6 flex-grow">Mengembangkan jiwa entrepreneur dan ide bisnis berbasis teknologi
                            dan
                            inovasi.</p>
                        <a href="../Kewirahusaan/index.html"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:from-purple-700 hover:to-indigo-700 hover:scale-[1.03] transition-transform duration-200 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Masuk Divisi
                        </a>
                    </div>
                </div>
                <div
                    class="bg-white rounded-xl shadow-md p-0 text-center flex flex-col overflow-hidden transform transition-transform duration-300 hover:-translate-y-2 shadow-md">
                    <div class=" h-44 w-full overflow-hidden">
                        <img src="images/pengabdian-masyarakat.jpg" alt="Divisi Pengabdian Masyarakat"
                            class="object-cover w-full h-full" />
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold mb-2 text-purple-700">Divisi Pengabdian Masyarakat</h3>
                        <p class="mb-6 flex-grow">Mengutamakan Kinera dan Kontribusi kepada masyarakat.</p>
                        <a href="../Pengabdian/index.html"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:from-purple-700 hover:to-indigo-700 hover:scale-[1.03] transition-transform duration-200 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Masuk Divisi
                        </a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-md p-0 text-center flex flex-col overflow-hidden transform transition-transform duration-300 hover:-translate-y-2 shadow-md">
                    <div class=" h-44 w-full overflow-hidden">
                        <img src="images/kesehatan.jpeg" alt="Divisi K3L" class="object-cover w-full h-full" />
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold mb-2 text-purple-700">Divisi Kesehatan, Keselamatan, dan
                            Lingkungan
                            (K3L)</h3>
                        <p class="mb-6 flex-grow">Mengedukasi dan mengelola aspek kesehatan dan keselamatan kerja
                            serta
                            lingkungan.</p>
                        <a href="../K3L/index.html"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:from-purple-700 hover:to-indigo-700 hover:scale-[1.03] transition-transform duration-200 ease-in-out">
                            Masuk Divisi
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-md p-0 text-center flex flex-col overflow-hidden transform transition-transform duration-300 hover:-translate-y-2 shadow-md">
                    <div class=" h-44 w-full overflow-hidden">
                        <img src="images/kreatif.jpg" alt="Divisi Kreativitas" class="object-cover w-full h-full" />
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-xl font-bold mb-2 text-purple-700">Divisi Kreativitas</h3>
                        <p class="mb-6 flex-grow">Mendorong inovasi dan kreativitas mahasiswa melalui berbagai
                            kegiatan
                            dan proyek.</p>
                        <a href="../Kreativitas/index.html"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:from-purple-700 hover:to-indigo-700 hover:scale-[1.03] transition-transform duration-200 ease-in-out">
                            Masuk Divisi
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
    </div>
    </section>

    <!-- Kontak dan Form -->
    <section id="kontak" class="bg-white rounded-2xl p-10 max-w-5xl mx-auto shadow-md fade-in fade-delay-4">
        <h2 class="text-4xl font-bold mb-10 text-center text-purple-700">Kontak Kami</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Form -->
            <form action="#" method="POST" class="space-y-6">
                <div>
                    <label for="nama" class="block mb-2 font-semibold text-gray-800">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap"
                        class="w-full rounded-lg border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-purple-600 transition" />
                </div>

                <div>
                    <label for="email" class="block mb-2 font-semibold text-gray-800">Email</label>
                    <input type="email" id="email" name="email" required placeholder="contoh@email.com"
                        class="w-full rounded-lg border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-purple-600 transition" />
                </div>

                <div>
                    <label for="pesan" class="block mb-2 font-semibold text-gray-800">Pesan</label>
                    <textarea id="pesan" name="pesan" rows="6" required placeholder="Tulis pesan Anda di sini..."
                        class="w-full rounded-lg border border-gray-300 p-3 resize-y focus:outline-none focus:ring-2 focus:ring-purple-600 transition"></textarea>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-lg bg-purple-700 text-white font-semibold hover:bg-purple-800 transition">
                    Kirim Pesan
                </button>
            </form>

            <!-- Kontak Info -->
            <div class="flex flex-col justify-center text-gray-700 space-y-6 text-lg">
                <img src="images/kontak.avif" alt="">
                <p><span class="font-semibold text-purple-700">Email:</span> <a href="mailto:info@klinikprodi.ac.id"
                        class="hover:underline text-purple-600">info@Technologymedicalcenter.ac.id</a></p>
                <p><span class="font-semibold text-purple-700">Telepon:</span> <a href="tel:+62123456789"
                        class="hover:underline text-purple-600">+62 123 456 789</a></p>
                <p><span class="font-semibold text-purple-700">Alamat:</span> Jl. Teknologi No.99, Kota Pendidikan,
                    Indonesia</p>
            </div>
        </div>
    </section>

    <footer class="text-center py-6 text-gray-500 text-sm select-none">
        &copy; 2025 Klinik Prodi - Technology Medical Center. All Rights Reserved.
    </footer>
    </div>

    <script>
        document.getElementById('userBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function() {
            const menu = document.getElementById('dropdownMenu');
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>

</html>