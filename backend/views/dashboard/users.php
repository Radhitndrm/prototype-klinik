<?php
// users.php

require '../../database/connection.php';

try {
    // Ambil semua user beserta divisinya (via mentor_divisi)
    $users = $pdo->query("
        SELECT 
            users.id_user, users.nama, users.email, users.role, divisi.id_divisi, divisi.nama_divisi
        FROM users
        LEFT JOIN mentor_divisi ON users.id_user = mentor_divisi.id_user
        LEFT JOIN divisi ON mentor_divisi.id_divisi = divisi.id_divisi
        ORDER BY users.nama
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Ambil semua divisi untuk dropdown di modal
    $all_divisi = $pdo->query("SELECT id_divisi, nama_divisi FROM divisi ORDER BY nama_divisi")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Pengguna - Klinik Prodi</title>
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
                        <a href="user.php"
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
                    class="w-full px-4 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Logout</button>
            </div>
        </aside>

        <!-- Main content wrapper -->
        <div class="flex-1 ml-64 flex flex-col">

            <!-- Header -->
            <header class="flex justify-between items-center bg-indigo-900 shadow-md p-4 sticky top-0 z-10">
                <div class="text-xl font-semibold text-white">Kelola Pengguna</div>
                <div class="flex items-center space-x-4">
                    <input type="search" placeholder="Search..."
                        class="px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 border border-indigo-700 bg-indigo-800 text-white placeholder-indigo-300" />
                    <div class="flex items-center space-x-3 cursor-pointer">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center font-bold text-white select-none">A
                        </div>
                        <div class="text-white font-medium">Admin</div>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="p-6 overflow-y-auto flex-1 bg-gray-50">
                <?php if (isset($_GET['success'])): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">Data pengguna berhasil diperbarui.</div>
                <?php endif; ?>

                <section id="users" class="mb-10">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded shadow">
                            <thead class="bg-indigo-700 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left">Nama</th>
                                    <th class="py-3 px-4 text-left">Email</th>
                                    <th class="py-3 px-4 text-left">Role</th>
                                    <th class="py-3 px-4 text-left">Divisi</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['nama']) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['email']) ?></td>
                                        <td class="py-3 px-4 capitalize"><?= htmlspecialchars($user['role']) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['nama_divisi'] ?? '-') ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <button class="edit-user-btn bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded shadow-sm transition-all"
                                                data-id="<?= $user['id_user'] ?>"
                                                data-nama="<?= htmlspecialchars($user['nama'], ENT_QUOTES) ?>"
                                                data-email="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>"
                                                data-role="<?= $user['role'] ?>"
                                                data-divisi-id="<?= $user['id_divisi'] ?? '' ?>">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- Modal Popup Edit User -->
    <div id="modal-edit-user" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative">
            <h3 class="text-xl font-semibold text-indigo-700 mb-4">Edit Pengguna</h3>
            <form id="form-edit-user" method="POST" action="../../functions/update_user.php">
                <input type="hidden" name="id_user" id="input-id-user" />
                <div class="mb-4">
                    <label for="input-nama-user" class="block text-gray-700 font-semibold mb-1">Nama</label>
                    <input type="text" id="input-nama-user" name="nama" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div class="mb-4">
                    <label for="input-email-user" class="block text-gray-700 font-semibold mb-1">Email</label>
                    <input type="email" id="input-email-user" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div class="mb-4">
                    <label for="input-role-user" class="block text-gray-700 font-semibold mb-1">Role</label>
                    <select id="input-role-user" name="role" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="anggota">Anggota</option>
                        <option value="mentor">Mentor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="input-divisi-user" class="block text-gray-700 font-semibold mb-1">Divisi (khusus Mentor)</label>
                    <select id="input-divisi-user" name="divisi_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" disabled>
                        <option value="">-- Pilih Divisi --</option>
                        <?php foreach ($all_divisi as $divisi): ?>
                            <option value="<?= $divisi['id_divisi'] ?>"><?= htmlspecialchars($divisi['nama_divisi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="btn-cancel" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
            <button id="btn-close-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-edit-user');
            const btnClose = document.getElementById('btn-close-modal');
            const btnCancel = document.getElementById('btn-cancel');

            const inputId = document.getElementById('input-id-user');
            const inputNama = document.getElementById('input-nama-user');
            const inputEmail = document.getElementById('input-email-user');
            const inputRole = document.getElementById('input-role-user');
            const inputDivisi = document.getElementById('input-divisi-user');

            function openModal(user) {
                inputId.value = user.id;
                inputNama.value = user.nama;
                inputEmail.value = user.email;
                inputRole.value = user.role;
                inputDivisi.value = user.divisiId || '';
                // Aktifkan dropdown divisi hanya jika role mentor
                inputDivisi.disabled = (user.role !== 'mentor');
                modal.classList.remove('hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
            }

            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const user = {
                        id: btn.dataset.id,
                        nama: btn.dataset.nama,
                        email: btn.dataset.email,
                        role: btn.dataset.role,
                        divisiId: btn.dataset.divisiId
                    };
                    openModal(user);
                });
            });

            // Jika role dropdown berubah, aktif/nonaktif divisi dropdown
            inputRole.addEventListener('change', function() {
                if (this.value === 'mentor') {
                    inputDivisi.disabled = false;
                } else {
                    inputDivisi.disabled = true;
                    inputDivisi.value = '';
                }
            });

            btnClose.addEventListener('click', closeModal);
            btnCancel.addEventListener('click', closeModal);

            // Klik luar modal untuk menutup
            window.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });
        });
    </script>
</body>

</html>