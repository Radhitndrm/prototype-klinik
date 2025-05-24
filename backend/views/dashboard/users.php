<?php
// users.php

require '../../database/connection.php'; // sudah ada $pdo

try {
    // Ambil data anggota dan mentor (role = 'anggota', 'mentor')
    $users = $pdo->prepare("SELECT id_user, nama, email, role FROM users WHERE role IN ('anggota', 'mentor') ORDER BY nama");
    $users->execute();
    $list_users = $users->fetchAll(PDO::FETCH_ASSOC);

    // Ambil data anggota (role = 'anggota')
    $anggota_only = $pdo->prepare("SELECT id_user, nama, email FROM users WHERE role = 'anggota' ORDER BY nama");
    $anggota_only->execute();
    $list_anggota_only = $anggota_only->fetchAll(PDO::FETCH_ASSOC);

    // Ambil data mentor beserta divisinya (gabungkan divisi jadi satu string)
    $mentor = $pdo->prepare("
        SELECT 
            users.id_user, users.nama, users.email, users.role,
            GROUP_CONCAT(divisi.id_divisi) AS divisi_ids,
            GROUP_CONCAT(divisi.nama_divisi SEPARATOR ', ') AS divisi_names
        FROM users
        LEFT JOIN mentor_divisi ON users.id_user = mentor_divisi.id_user
        LEFT JOIN divisi ON mentor_divisi.id_divisi = divisi.id_divisi
        WHERE users.role = 'mentor'
        GROUP BY users.id_user
        ORDER BY users.nama
    ");
    $mentor->execute();
    $list_mentor = $mentor->fetchAll(PDO::FETCH_ASSOC);

    // Ambil semua divisi untuk dropdown di modal
    $divisi_stmt = $pdo->prepare("SELECT id_divisi, nama_divisi FROM divisi ORDER BY nama_divisi");
    $divisi_stmt->execute();
    $all_divisi = $divisi_stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    class="w-full px-4 py-2 rounded bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Logout</button>
            </div>
        </aside>

        <!-- Main content wrapper -->
        <div class="flex-1 ml-64 flex flex-col">

            <!-- Header -->
            <header class="flex justify-between items-center bg-purple-900 shadow-md p-4 sticky top-0 z-10">
                <div class="text-xl font-semibold text-white">Kelola Pengguna</div>
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

                <?php if (isset($_GET['success-user'])): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">Data pengguna berhasil diperbarui.</div>
                <?php endif; ?>
                <?php if (isset($_GET['success-mentor'])): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">Data mentor berhasil diperbarui.</div>
                <?php endif; ?>
                <?php if (isset($_GET['success-delete-user'])): ?>
                    <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">Data User berhasil dihapus.</div>
                <?php endif; ?>

                <!-- Tabel Anggota -->
                <section id="anggota" class="mb-10">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-semibold">Daftar Anggota</h2>
                        <!-- Tombol Tambah Mentor Baru -->
                        <button id="open-add-mentor-modal" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">
                            + Tambah Mentor Baru
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded shadow">
                            <thead class="bg-purple-700 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left">Nama</th>
                                    <th class="py-3 px-4 text-left">Email</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list_users as $user): ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['nama']) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['email']) ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <button class="edit-user-btn bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded shadow-sm transition-all"
                                                data-id="<?= $user['id_user'] ?>"
                                                data-nama="<?= htmlspecialchars($user['nama'], ENT_QUOTES) ?>"
                                                data-email="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>"
                                                data-role="<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>">
                                                Edit
                                            </button>
                                            <button class="delete-user-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow-sm transition-all"
                                                data-id="<?= $user['id_user'] ?>"
                                                data-nama="<?= htmlspecialchars($user['nama'], ENT_QUOTES) ?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Tabel Mentor -->
                <section id="mentor" class="mb-10">
                    <h2 class="text-2xl font-semibold mb-4">Daftar Mentor</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded shadow">
                            <thead class="bg-purple-700 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left">Nama</th>
                                    <th class="py-3 px-4 text-left">Email</th>
                                    <th class="py-3 px-4 text-left">Divisi</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list_mentor as $user): ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['nama']) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['email']) ?></td>
                                        <td class="py-3 px-4"><?= htmlspecialchars($user['divisi_names'] ?? '-') ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <button class="edit-mentor-btn bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded shadow-sm transition-all"
                                                data-id="<?= $user['id_user'] ?>"
                                                data-nama="<?= htmlspecialchars($user['nama'], ENT_QUOTES) ?>"
                                                data-email="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>"
                                                data-role="<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>"
                                                data-divisi-ids="<?= htmlspecialchars($user['divisi_ids'] ?? '') ?>">
                                                Edit
                                            </button>
                                            <button class="delete-mentor-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow-sm transition-all"
                                                data-id="<?= $user['id_user'] ?>"
                                                data-nama="<?= htmlspecialchars($user['nama'], ENT_QUOTES) ?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Modal Edit User (untuk role anggota) -->
                <div id="modal-edit-user" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg relative">
                        <h3 class="text-xl font-semibold mb-4">Edit Pengguna</h3>
                        <form id="edit-user-form" method="post" action="../../functions/edit_user_basic.php">
                            <input type="hidden" name="id_user" id="edit-user-id_user" />
                            <div class="mb-4">
                                <label for="edit-user-nama" class="block mb-1 font-semibold">Nama</label>
                                <input type="text" id="edit-user-nama" name="nama" required
                                    class="w-full border border-gray-300 rounded px-3 py-2" />
                            </div>
                            <div class="mb-4">
                                <label for="edit-user-email" class="block mb-1 font-semibold">Email</label>
                                <input type="email" id="edit-user-email" name="email" required
                                    class="w-full border border-gray-300 rounded px-3 py-2" />
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" id="cancel-edit-user"
                                    class="px-4 py-2 rounded border border-gray-400 hover:bg-gray-100 transition">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 rounded bg-purple-700 text-white hover:bg-purple-800 transition">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Tambah Mentor Baru (convert anggota jadi mentor + pilih divisi) -->
                <div id="modal-add-mentor" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg relative">
                        <h3 class="text-xl font-semibold mb-4">Tambah Mentor Baru</h3>
                        <form id="add-mentor-form" method="post" action="../../functions/add_mentor.php">
                            <div class="mb-4">
                                <label for="add-mentor-id_user" class="block mb-1 font-semibold">Pilih Anggota</label>
                                <select id="add-mentor-id_user" name="id_user" required
                                    class="w-full border border-gray-300 rounded px-3 py-2">
                                    <option value="" disabled selected>-- Pilih anggota --</option>
                                    <?php foreach ($list_anggota_only as $anggota): ?>
                                        <option value="<?= $anggota['id_user'] ?>">
                                            <?= htmlspecialchars($anggota['nama']) ?> (<?= htmlspecialchars($anggota['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="add-mentor-divisi" class="block mb-1 font-semibold">Pilih Divisi</label>
                                <select id="add-mentor-divisi" name="divisi_ids[]" multiple required
                                    class="w-full border border-gray-300 rounded px-3 py-2 h-32 overflow-y-auto">
                                    <?php foreach ($all_divisi as $div): ?>
                                        <option value="<?= $div['id_divisi'] ?>"><?= htmlspecialchars($div['nama_divisi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-gray-600">Tekan Ctrl (Cmd) untuk memilih lebih dari satu divisi.</small>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" id="cancel-add-mentor"
                                    class="px-4 py-2 rounded border border-gray-400 hover:bg-gray-100 transition">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 rounded bg-green-700 text-white hover:bg-green-800 transition">Tambah Mentor</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Edit Mentor (untuk update divisi mentor) -->
                <div id="modal-edit-mentor" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg relative">
                        <h3 class="text-xl font-semibold mb-4">Edit Mentor</h3>
                        <form id="edit-mentor-form" method="post" action="../../functions/update_mentor.php">
                            <input type="hidden" name="id_user" id="edit-mentor-id_user" />
                            <div class="mb-4">
                                <label for="edit-mentor-nama" class="block mb-1 font-semibold">Nama</label>
                                <input type="text" id="edit-mentor-nama" name="nama"
                                    class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" disabled />
                            </div>
                            <div class="mb-4">
                                <label for="edit-mentor-email" class="block mb-1 font-semibold">Email</label>
                                <input type="email" id="edit-mentor-email" name="email"
                                    class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" disabled />
                            </div>
                            <div class="mb-4">
                                <label for="edit-mentor-divisi" class="block mb-1 font-semibold">Pilih Divisi</label>
                                <select id="edit-mentor-divisi" name="divisi_ids[]" multiple required
                                    class="w-full border border-gray-300 rounded px-3 py-2 h-32 overflow-y-auto">
                                    <?php foreach ($all_divisi as $div): ?>
                                        <option value="<?= $div['id_divisi'] ?>"><?= htmlspecialchars($div['nama_divisi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-gray-600">Tekan Ctrl (Cmd) untuk memilih lebih dari satu divisi.</small>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" id="cancel-edit-mentor"
                                    class="px-4 py-2 rounded border border-gray-400 hover:bg-gray-100 transition">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 rounded bg-purple-700 text-white hover:bg-purple-800 transition">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // Modal elements
        const modalEditUser = document.getElementById('modal-edit-user');
        const modalAddMentor = document.getElementById('modal-add-mentor');
        const modalEditMentor = document.getElementById('modal-edit-mentor');

        // Tombol batal di semua modal
        document.getElementById('cancel-edit-user').addEventListener('click', () => {
            modalEditUser.classList.add('hidden');
        });
        document.getElementById('cancel-add-mentor').addEventListener('click', () => {
            modalAddMentor.classList.add('hidden');
        });
        document.getElementById('cancel-edit-mentor').addEventListener('click', () => {
            modalEditMentor.classList.add('hidden');
        });

        // Tombol buka modal tambah mentor baru
        document.getElementById('open-add-mentor-modal').addEventListener('click', () => {
            modalAddMentor.classList.remove('hidden');
        });

        // Tombol edit user (anggota / mentor tanpa edit divisi)
        document.querySelectorAll('.edit-user-btn').forEach(button => {
            button.addEventListener('click', () => {
                const idUser = button.dataset.id;
                const nama = button.dataset.nama;
                const email = button.dataset.email;
                const role = button.dataset.role || 'anggota';


                // Isi form modal edit user
                document.getElementById('edit-user-id_user').value = idUser;
                document.getElementById('edit-user-nama').value = nama;
                document.getElementById('edit-user-email').value = email;
                // document.getElementById('edit-user-role').value = role;

                modalEditUser.classList.remove('hidden');
            });
        });

        // Tombol edit mentor (buka modal edit mentor)
        document.querySelectorAll('.edit-mentor-btn').forEach(button => {
            button.addEventListener('click', () => {
                const idUser = button.dataset.id;
                const nama = button.dataset.nama;
                const email = button.dataset.email;
                const role = button.dataset.role || 'mentor';
                const divisiIds = button.dataset.divisiIds || '';

                if (role !== 'mentor') {
                    alert('Data ini bukan mentor.');
                    return;
                }

                // Isi modal edit mentor
                document.getElementById('edit-mentor-id_user').value = idUser;
                document.getElementById('edit-mentor-nama').value = nama;
                document.getElementById('edit-mentor-email').value = email;

                // Reset pilihan divisi
                const selectDivisi = document.getElementById('edit-mentor-divisi');
                Array.from(selectDivisi.options).forEach(option => option.selected = false);

                // Pilih sesuai divisi_ids
                if (divisiIds.length > 0) {
                    const arrDivisi = divisiIds.split(',');
                    arrDivisi.forEach(id => {
                        const opt = selectDivisi.querySelector(`option[value="${id}"]`);
                        if (opt) opt.selected = true;
                    });
                }

                modalEditMentor.classList.remove('hidden');
            });
        });
        // Tombol delete user (konfirmasi)
        document.querySelectorAll('.delete-user-btn').forEach(button => {
            button.addEventListener('click', () => {
                const idUser = button.dataset.id;
                const nama = button.dataset.nama;
                if (confirm(`Yakin ingin menghapus pengguna "${nama}"?`)) {
                    fetch('../../functions/delete_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id_user=${encodeURIComponent(idUser)}`
                        })
                        .then(res => res.text())
                        .then(data => {
                            alert(data);
                            location.reload();
                        })
                        .catch(err => alert('Terjadi kesalahan: ' + err));
                }
            });
        });

        // Tombol delete mentor (konfirmasi)
        document.querySelectorAll('.delete-mentor-btn').forEach(button => {
            button.addEventListener('click', () => {
                const idUser = button.dataset.id;
                const nama = button.dataset.nama;
                if (confirm(`Yakin ingin menghapus mentor "${nama}"? Role akan diubah menjadi anggota.`)) {
                    fetch('../../functions/delete_mentor.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id_user=${encodeURIComponent(idUser)}`
                        }).then(res => res.text())
                        .then(data => {
                            alert(data);
                            location.reload();
                        })
                        .catch(err => alert('Terjadi kesalahan: ' + err));
                }
            });
        });
    </script>
</body>

</html>