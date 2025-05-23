<?php
// divisi.php

require '../../database/connection.php';

try {
    $divisi = $pdo->query("SELECT * FROM divisi")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data divisi: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Divisi - Klinik Prodi</title>
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
                        <a href="index.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 12l2-2 4 4 8-8 2 2v6H3z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="divisi.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-purple-700 text-white transition">
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
                        <a href="reports.php"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 12h18M9 6h6M9 18h6" />
                            </svg>
                            Reports
                        </a>
                        <a href="settings.php"
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
                    class="w-full px-4 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Logout</button>
            </div>
        </aside>

        <!-- Main content wrapper -->
        <div class="flex-1 ml-64 flex flex-col">

            <!-- Header -->
            <header class="flex justify-between items-center bg-purple-900 shadow-md p-4 sticky top-0 z-10">
                <div class="text-xl font-semibold text-white">Kelola Divisi</div>
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

            <!-- Main content -->
            <main class="p-6 overflow-y-auto flex-1 bg-gray-50">

                <?php if (isset($_GET['success'])): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">Data divisi berhasil diperbarui.</div>
                <?php endif; ?>

                <section id="divisi" class="mb-10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ($divisi as $d): ?>
                            <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
                                <h4 class="font-semibold text-purple-700 mb-3"><?= htmlspecialchars($d['nama_divisi']) ?></h4>
                                <p class="text-gray-700 mb-4"><?= htmlspecialchars($d['desc_divisi']) ?></p>
                                <?php if (!empty($d['gambar_divisi'])): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($d['gambar_divisi']) ?>" alt="Gambar Divisi" class="mb-3 w-full h-40 object-cover rounded" />
                                <?php endif; ?>
                                <button
                                    class="edit-divisi-btn bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded"
                                    data-id="<?= $d['id_divisi'] ?>"
                                    data-nama="<?= htmlspecialchars($d['nama_divisi'], ENT_QUOTES) ?>"
                                    data-deskripsi="<?= htmlspecialchars($d['desc_divisi'], ENT_QUOTES) ?>"
                                    data-gambar="<?= htmlspecialchars($d['gambar_divisi'], ENT_QUOTES) ?>">
                                    Edit
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            </main>

        </div>

    </div>

    <!-- Modal Popup Edit Divisi -->
    <div id="modal-edit-divisi" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg w-96 p-6 shadow-lg relative">
            <h3 class="text-xl font-semibold text-purple-700 mb-4">Edit Divisi</h3>
            <form id="form-edit-divisi" method="POST" action="../../functions/update_divisi.php" enctype="multipart/form-data">
                <input type="hidden" name="id_divisi" id="input-id-divisi" />
                <input type="hidden" name="gambar_lama" id="input-gambar-lama" />
                <div class="mb-4">
                    <label for="input-nama-divisi" class="block text-gray-700 font-semibold mb-1">Nama Divisi</label>
                    <input type="text" id="input-nama-divisi" name="nama_divisi" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                </div>
                <div class="mb-4">
                    <label for="input-deskripsi-divisi" class="block text-gray-700 font-semibold mb-1">Deskripsi</label>
                    <textarea id="input-deskripsi-divisi" name="desc_divisi" rows="3" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">Gambar Divisi Saat Ini</label>
                    <img id="preview-gambar-lama" src="" alt="Preview Gambar Lama" class="w-full h-40 object-cover rounded mb-3" />
                </div>

                <div class="mb-4">
                    <label for="input-gambar-divisi" class="block text-gray-700 font-semibold mb-1">Upload Gambar Baru (opsional)</label>
                    <input type="file" id="input-gambar-divisi" name="gambar_divisi" accept="image/*"
                        class="w-full" />
                    <img id="preview-gambar-baru" src="" alt="Preview Gambar Baru" class="hidden w-full h-40 object-cover rounded mt-3" />
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="btn-cancel" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">Simpan</button>
                </div>
            </form>
            <button id="btn-close-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-edit-divisi');
            const btnClose = document.getElementById('btn-close-modal');
            const btnCancel = document.getElementById('btn-cancel');

            const inputId = document.getElementById('input-id-divisi');
            const inputNama = document.getElementById('input-nama-divisi');
            const inputDeskripsi = document.getElementById('input-deskripsi-divisi');

            const inputGambarLama = document.getElementById('input-gambar-lama');
            const previewGambarLama = document.getElementById('preview-gambar-lama');
            const inputGambarBaru = document.getElementById('input-gambar-divisi');
            const previewGambarBaru = document.getElementById('preview-gambar-baru');

            function openModal(divisi) {
                inputId.value = divisi.id;
                inputNama.value = divisi.nama;
                inputDeskripsi.value = divisi.deskripsi;

                inputGambarLama.value = divisi.gambar || '';
                if (divisi.gambar) {
                    previewGambarLama.src = '../../uploads/' + divisi.gambar;
                    previewGambarLama.classList.remove('hidden');
                } else {
                    previewGambarLama.src = '';
                    previewGambarLama.classList.add('hidden');
                }

                // Reset input gambar baru dan preview
                inputGambarBaru.value = '';
                previewGambarBaru.src = '';
                previewGambarBaru.classList.add('hidden');

                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
            }

            // Event listeners untuk tombol edit
            document.querySelectorAll('.edit-divisi-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const divisi = {
                        id: btn.dataset.id,
                        nama: btn.dataset.nama,
                        deskripsi: btn.dataset.deskripsi,
                        gambar: btn.dataset.gambar
                    };
                    openModal(divisi);
                });
            });

            btnClose.addEventListener('click', closeModal);
            btnCancel.addEventListener('click', closeModal);

            // Preview gambar baru saat dipilih
            inputGambarBaru.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewGambarBaru.src = e.target.result;
                        previewGambarBaru.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewGambarBaru.src = '';
                    previewGambarBaru.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>