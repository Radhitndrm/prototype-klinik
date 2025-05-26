<?php
session_start();
require '../../database/connection.php';
require '../../functions/mentor/fetch_konten.php';
require '../../functions/mentor/add_konten.php';
require '../../functions/mentor/update_konten.php';
require '../../functions/mentor/fetch.php';

// Cek autentikasi
if (!isset($_SESSION['id_user']) || !isset($_SESSION['divisi_mentor'])) {
    header("Location: ../../login.php");
    exit;
}

// Fungsi hapus konten beserta file gambar terkait
function deleteKonten(PDO $pdo, string $id_konten, string $uploadDir = '../../uploads/')
{
    $stmt = $pdo->prepare("SELECT gambar_url FROM konten_divisi WHERE id_konten = ?");
    $stmt->execute([$id_konten]);
    $file = $stmt->fetchColumn();

    if ($file) {
        $filePath = $uploadDir . basename($file);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM konten_divisi WHERE id_konten = ?");
    $stmt->execute([$id_konten]);
}

$id_user = $_SESSION['id_user'];
$nama_mentor = fetchColumnById($pdo, "SELECT nama FROM users WHERE id_user = ?", $id_user);

$divisi_id_mentor = $_SESSION['divisi_mentor'];
$divisi_mentor_nama = is_array($divisi_id_mentor) ? $divisi_id_mentor[0] : $divisi_id_mentor;

$stmt = $pdo->prepare("SELECT id_divisi FROM divisi WHERE nama_divisi = ?");
$stmt->execute([$divisi_mentor_nama]);
$id_divisi = $stmt->fetchColumn();

$divisi_id = $id_divisi;

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_konten = $_POST['id_konten'] ?? null;
    $judul = $_POST['judul'] ?? '';
    $gambar = $_FILES['gambar_url'] ?? null;

    try {
        if ($id_konten) {
            updateKonten($pdo, $id_konten, $judul, $divisi_id, $id_user, $gambar);
        } else {
            addKonten($pdo, $judul, $divisi_id, $id_user, $gambar);
        }
        header("Location: content.php");
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id_konten = $_GET['delete'];
    deleteKonten($pdo, $id_konten);
    header("Location: content.php");
    exit;
}

// Ambil konten
$stmt = $pdo->prepare("
    SELECT kd.*, d.nama_divisi 
    FROM konten_divisi kd
    JOIN divisi d ON kd.divisi_id = d.id_divisi
    WHERE kd.divisi_id = ? AND kd.mentor_id = ?
    ORDER BY kd.tanggal_upload DESC
");
$stmt->execute([$divisi_id, $id_user]);
$kontenList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Kelola Konten Divisi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <aside class="w-64 bg-gray-800 fixed inset-y-0 left-0 flex flex-col justify-between border-r border-gray-700 shadow-lg">
        <div>
            <div class="px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-200 mb-10">Klinik Prodi</h1>
                <nav class="flex flex-col space-y-3 text-gray-300">
                    <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 hover:text-white transition">
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

    <header class="fixed top-0 left-64 right-0 bg-purple-900 shadow-md p-4 flex justify-between items-center z-20">
        <div class="text-xl font-semibold text-white select-none">Kelola Konten Divisi</div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-3 cursor-pointer select-none">
                <div class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center font-bold text-white">
                    <?= htmlspecialchars(substr($nama_mentor, 0, 1)) ?>
                </div>
                <div class="text-white font-medium whitespace-nowrap"><?= htmlspecialchars($nama_mentor) ?></div>
            </div>
        </div>
    </header>

    <main class="flex-1 ml-64 p-10 mt-16">
        <h2 class="text-2xl font-semibold text-gray-800 mb-8">Konten Divisi: <?= htmlspecialchars($divisi_mentor_nama) ?></h2>

        <button id="openCreateModalBtn" class="mb-6 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">Tambah Konten</button>

        <!-- Modal Create -->
        <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button id="closeCreateModalBtn" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
                <h3 class="text-xl font-semibold mb-4">Tambah Konten Baru</h3>
                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="id_konten" value="">
                    <div>
                        <label class="block font-medium mb-1">Judul Konten</label>
                        <input name="judul" type="text" required class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Divisi</label>
                        <input type="text" value="<?= htmlspecialchars($divisi_mentor_nama) ?>" disabled class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Upload Gambar/Video (opsional)</label>
                        <input name="gambar_url" type="file" accept="image/*,video/*" class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">Tambah Konten</button>
                </form>
            </div>
        </div>

        <!-- Modal Update -->
        <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button id="closeUpdateModalBtn" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
                <h3 class="text-xl font-semibold mb-4">Edit Konten</h3>
                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="id_konten" value="">
                    <div>
                        <label class="block font-medium mb-1">Judul Konten</label>
                        <input name="judul" type="text" required class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Divisi</label>
                        <input type="text" value="<?= htmlspecialchars($divisi_mentor_nama) ?>" disabled class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Upload Gambar/Video (biarkan kosong jika tidak ingin ganti)</label>
                        <input name="gambar_url" type="file" accept="image/*,video/*" class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">Update Konten</button>
                </form>
            </div>
        </div>

        <table class="w-full border-collapse table-auto text-sm border border-gray-300 rounded-md">
            <thead class="bg-purple-600 text-white">
                <tr>
                    <th class="px-4 py-2 border border-purple-700">No</th>
                    <th class="px-4 py-2 border border-purple-700">Judul Konten</th>
                    <th class="px-4 py-2 border border-purple-700">Gambar/Video</th>
                    <th class="px-4 py-2 border border-purple-700">Tanggal Upload</th>
                    <th class="px-4 py-2 border border-purple-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($kontenList) === 0) : ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Belum ada konten.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kontenList as $i => $konten) : ?>
                        <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?>">
                            <td class="px-4 py-2 border border-gray-300 text-center"><?= $i + 1 ?></td>
                            <td class="px-4 py-2 border border-gray-300"><?= htmlspecialchars($konten['judul']) ?></td>
                            <td class="px-4 py-2 border border-gray-300">
                                <?php if ($konten['gambar_url']) : ?>
                                    <?php
                                    $ext = pathinfo($konten['gambar_url'], PATHINFO_EXTENSION);
                                    if (in_array(strtolower($ext), ['mp4', 'webm', 'ogg'])) :
                                    ?>
                                        <video width="160" controls>
                                            <source src="../../backend/uploads/konten/<?= htmlspecialchars($konten['gambar_url']) ?>" type="video/<?= $ext ?>">
                                            Browser anda tidak mendukung video.
                                        </video>
                                    <?php else: ?>
                                        <img src="../../uploads/konten/<?= htmlspecialchars($konten['gambar_url']) ?>" alt="Gambar Konten" class="max-w-[160px] max-h-24 object-cover rounded" />
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?= date('d M Y', strtotime($konten['tanggal_upload'])) ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center space-x-1">
                                <button
                                    class="editBtn bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-sm font-semibold"
                                    data-id="<?= $konten['id_konten'] ?>"
                                    data-judul="<?= htmlspecialchars($konten['judul']) ?>">
                                    Edit
                                </button>
                                <a href="?delete=<?= $konten['id_konten'] ?>" onclick="return confirm('Yakin ingin menghapus konten ini?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <script>
        const createModal = document.getElementById('createModal');
        const updateModal = document.getElementById('updateModal');

        document.getElementById('openCreateModalBtn').onclick = () => {
            createModal.classList.remove('hidden');
        };
        document.getElementById('closeCreateModalBtn').onclick = () => {
            createModal.classList.add('hidden');
        };
        document.getElementById('closeUpdateModalBtn').onclick = () => {
            updateModal.classList.add('hidden');
        };

        // Edit button click: isi form modal update dan tampilkan modal
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const judul = btn.dataset.judul;

                const form = updateModal.querySelector('form');
                form.id_konten.value = id;
                form.judul.value = judul;

                updateModal.classList.remove('hidden');
            });
        });

        // Klik di luar modal untuk menutup modal (optional)
        [createModal, updateModal].forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>