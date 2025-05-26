<?php
require_once '../../backend/database/connection.php'; // file yang sudah bikin variabel $pdo

// $pdo sudah tersedia dari connection.php
$sqlMateri = "
    SELECT m.judul, m.file_url, m.file_url, d.nama_divisi AS kategori
    FROM materi m
    LEFT JOIN divisi d ON m.divisi_id = d.id_divisi
    WHERE d.nama_divisi = 'Divisi Fullstack'
    ORDER BY m.tanggal_upload DESC
";
$stmt = $pdo->query($sqlMateri);
$materi = $stmt->fetchAll();

$sqlPrestasi = "
    SELECT k.judul, k.gambar_url, d.nama_divisi
    FROM konten_divisi k
    LEFT JOIN divisi d ON k.divisi_id = d.id_divisi
    WHERE d.nama_divisi =  'Divisi Fullstack'
    ORDER BY k.tanggal_upload DESC
";
$stmt2 = $pdo->query($sqlPrestasi);
$prestasi = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Klinik | Belajar HTML & CSS - Tailwind</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }

        /* Keyframes untuk animasi fadeIn */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Kelas untuk menerapkan animasi fadeIn */
        .fade-in-effect {
            animation: fadeIn 1s ease-in;
        }

        /* Container preview awalnya hidden */
        #materi-preview {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.7s ease, opacity 0.7s ease;
        }
    </style>
    <link rel="icon" href="https://placehold.co/32x32/8A2BE2/FFFFFF?text=FS&font=sans-serif" type="image/png" />
</head>

<body class="leading-relaxed bg-gradient-to-r from-[#f8fbff] to-[#e0f0ff] text-[#333]">
    <div class="max-w-6xl mx-auto p-5">

        <nav class="flex justify-between items-center p-5 bg-white/90 backdrop-blur-sm rounded-xl shadow-[0_2px_12px_rgba(0,0,0,0.08)] mb-10 sticky top-5 z-50 mx-auto">
            <img src="/frontend/main/images/logo.png" alt="Logo Klinik" class="rounded-2xl shadow-md shadow-indigo-500/50 max-w-[200px]" />
            <ul class="hidden md:flex gap-8 list-none">
                <li><a href="#home" class="text-[#4a4a4a] font-semibold transition-colors duration-300 ease-in-out hover:text-[#8a2be2]">Beranda</a></li>
                <li><a href="#materi" class="text-[#4a4a4a] font-semibold transition-colors duration-300 ease-in-out hover:text-[#8a2be2]">Materi</a></li>
                <li><a href="#project" class="text-[#4a4a4a] font-semibold transition-colors duration-300 ease-in-out hover:text-[#8a2be2]">Contoh Project</a></li>
                <li><a href="#kontak" class="text-[#4a4a4a] font-semibold transition-colors duration-300 ease-in-out hover:text-[#8a2be2]">Kontak</a></li>
            </ul>
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-[#4a4a4a] focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-white/90 backdrop-blur-sm rounded-xl shadow-lg mt-2 py-2 sticky top-[85px] z-40 mx-auto">
            <a href="#home"
                class="block px-4 py-2 text-[#4a4a4a] font-semibold hover:bg-[#f0e6ff] hover:text-[#8a2be2]">Beranda</a>
            <a href="#materi"
                class="block px-4 py-2 text-[#4a4a4a] font-semibold hover:bg-[#f0e6ff] hover:text-[#8a2be2]">Materi</a>
            <a href="#project"
                class="block px-4 py-2 text-[#4a4a4a] font-semibold hover:bg-[#f0e6ff] hover:text-[#8a2be2]">Contoh
                Project</a>
            <a href="#kontak"
                class="block px-4 py-2 text-[#4a4a4a] font-semibold hover:bg-[#f0e6ff] hover:text-[#8a2be2]">Kontak</a>
        </div>

        <header id="home"
            class="text-center py-16 px-5 bg-gradient-to-br from-[#d6e4ff] to-[#f5eaff] rounded-2xl mt-12 mb-12 shadow-[0_4px_12px_rgba(0,0,0,0.05)]">
            <h1 class="text-3xl md:text-[2.7rem] mb-2.5 font-bold">Divisi <span class="text-[#8a2be2]">Full-Stack
                    Developer</span></h1>
            <p class="text-lg md:text-xl text-[#5e5e5e] mb-4">Membangun Komunitas Full-Stack hingga sampai ke puncak.
            </p>
            <a href="../main/index.php">
                <button
                    class="mt-[15px] py-3 px-5 bg-gradient-to-r from-[#8a2be2] to-[#5c83f3] text-white border-none rounded-lg font-semibold shadow-[0_4px_8px_rgba(138,43,226,0.2)] cursor-pointer transition-all duration-300 ease-in-out hover:from-[#7a1fd9] hover:to-[#4c6fe0] hover:-translate-y-0.5">
                    Kembali ke halaman utama
                </button>
            </a>
        </header>

        <section id="materi"
            class="bg-white rounded-[18px] px-[30px] py-10 mb-12 shadow-[0_6px_22px_rgba(0,0,0,0.05)] fade-in-effect max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-[#2b2b2b]">📈 Materi Fullstack</h2>
            <div class="flex flex-wrap gap-[15px] mt-2.5">
                <?php foreach ($materi as $m): ?>
                    <button
                        class="py-3 px-[22px] bg-gradient-to-r from-[#6366f1] to-[#8b5cf6] text-white border-none rounded-lg font-semibold shadow-[0_4px_10px_rgba(139,92,246,0.25)] cursor-pointer transition-all duration-300 ease-in-out hover:from-[#4f46e5] hover:to-[#7c3aed] hover:-translate-y-0.5"
                        data-file-url="<?= htmlspecialchars($m['file_url']) ?>"
                        data-judul="<?= htmlspecialchars($m['judul']) ?>">
                        <?= htmlspecialchars($m['judul']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Tempat menampilkan preview materi -->
            <div id="materi-preview" class="mt-6 rounded-lg bg-[#f3f4f6] p-6 shadow-md text-center">
                <!-- Konten preview muncul di sini -->
            </div>
        </section>

        <section id="project"
            class="bg-white rounded-2xl px-[30px] py-10 mb-12 shadow-[0_6px_18px_rgba(0,0,0,0.04)] fade-in-effect">
            <h2 class="text-2xl font-bold mb-6 text-[#333]">🏆 Prestasi Divisi Full-Stack </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pb-4">
                <?php foreach ($prestasi as $p): ?>
                    <div
                        class="bg-[#fdfdff] rounded-xl overflow-hidden shadow-[0_4px_14px_rgba(0,0,0,0.06)] transition-transform duration-300 ease-in-out hover:-translate-y-1.5 pb-4">
                        <img src="../../backend/uploads/konten/<?= htmlspecialchars($p['gambar_url']) ?>" alt="<?= htmlspecialchars($p['judul']) ?>"
                            class="w-full h-[160px] md:h-[180px] object-cover" />
                        <h3 class="px-[15px] pt-[15px] pb-[10px] text-[#222] font-semibold text-base sm:text-lg">
                            <?= htmlspecialchars($p['judul']) ?>
                        </h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <footer id="kontak" class="text-center mb-16 text-sm text-[#888]">Copyright 2023 Klinik | Belajar HTML & CSS
            Tailwind</footer>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Preview materi dengan animasi fade-in
        const materiPreview = document.getElementById('materi-preview');
        const buttons = document.querySelectorAll('#materi button');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const fileUrl = button.getAttribute('data-file-url');
                const judul = button.getAttribute('data-judul');
                if (!fileUrl) return;

                // Tentukan tipe file berdasarkan ekstensi
                const ext = fileUrl.split('.').pop().toLowerCase();
                let contentHTML = '';

                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    contentHTML = `<img src="../../backend/uploads/materi/${fileUrl}" alt="${judul}" class="max-w-full rounded-lg shadow-lg mx-auto" />`;
                } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
                    contentHTML = `
                        <video controls class="max-w-full rounded-lg shadow-lg mx-auto">
                            <source src="../../uploads/materi/${fileUrl}" type="video/${ext}">
                            Browsermu tidak mendukung tag video.
                        </video>
                    `;
                } else {
                    // File lain ditampilkan sebagai link download
                    contentHTML = `<a href="../../uploads/materi/${fileUrl}" target="_blank" class="text-blue-600 underline">${judul} (Download / Lihat file)</a>`;
                }

                // Masukkan konten ke container preview
                materiPreview.innerHTML = `
                    <h3 class="text-xl font-semibold mb-4 text-center">${judul}</h3>
                    ${contentHTML}
                `;

                // Tampilkan dengan animasi fade-in
                materiPreview.style.maxHeight = '1000px'; // nilai besar untuk membuka area
                materiPreview.style.opacity = '1';
                materiPreview.classList.add('fade-in-effect');

                // Scroll ke konten supaya langsung terlihat
                materiPreview.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });
    </script>
</body>

</html>