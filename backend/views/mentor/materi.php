<?php
session_start();
require '../../database/connection.php';
require '../../functions/mentor/fetch.php';
require '../../functions/mentor/add_materi.php';
require '../../functions/mentor/update_materi.php';
/**
 * Fungsi menghapus materi beserta file video terkait
 * @param PDO $pdo
 * @param string $id_materi
 * @param string $uploadDir Direktori tempat file video disimpan, default ../../uploads/
 */
function deleteMateri(PDO $pdo, string $id_materi, string $uploadDir = '../../uploads/')
{
    // Ambil file_url dari database
    $stmt = $pdo->prepare("SELECT file_url FROM materi WHERE id_materi = ?");
    $stmt->execute([$id_materi]);
    $file = $stmt->fetchColumn();

    if ($file) {
        // Pastikan hanya menghapus file di folder upload untuk keamanan
        $filePath = $uploadDir . basename($file);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus data materi dari database
    $stmt = $pdo->prepare("DELETE FROM materi WHERE id_materi = ?");
    $stmt->execute([$id_materi]);
}

// Ambil data user dan divisi mentor
$id_user = $_SESSION['id_user'];
$nama_mentor = fetchColumnById($pdo, "SELECT nama FROM users WHERE id_user = ?", $id_user);

$divisi_id_mentor = $_SESSION['divisi_mentor'];
$divisi_mentor_nama = is_array($divisi_id_mentor) ? $divisi_id_mentor[0] : $divisi_id_mentor;

$stmt = $pdo->prepare("SELECT id_divisi FROM divisi WHERE nama_divisi = ?");
$stmt->execute([$divisi_mentor_nama]);
$id_divisi = $stmt->fetchColumn();

// Handle form POST untuk tambah/update materi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_materi = $_POST['id_materi'] ?? null;
    $judul = $_POST['judul'] ?? '';
    $divisi_id = $_POST['divisi_id'] ?? 0;
    $file = $_FILES['file_url'] ?? null;

    try {
        if ($id_materi) {
            updateMateri($pdo, $id_materi, $judul, $divisi_id, $file);
        } else {
            createMateri($pdo, $judul, $divisi_id, $file);
        }
        header("Location: materi.php");
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Handle delete lewat GET parameter
if (isset($_GET['delete'])) {
    $id_materi = $_GET['delete']; // Jangan cast ke int, tetap string
    deleteMateri($pdo, $id_materi);
    header("Location: materi.php"); // Redirect ke materi.php yang sesuai
    exit;
}

// Ambil daftar materi divisi mentor
$stmt = $pdo->prepare("
    SELECT m.*, d.nama_divisi 
    FROM materi m
    JOIN divisi d ON m.divisi_id = d.id_divisi
    WHERE m.divisi_id = ?
    ORDER BY m.tanggal_upload DESC
");
$stmt->execute([$id_divisi]);
$materiList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>CRUD Materi</title>
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
    <header
        class="fixed top-0 left-64 right-0 bg-purple-900 shadow-md p-4 flex justify-between items-center z-20">
        <div class="text-xl font-semibold text-white select-none">Dashboard Mentor</div>
        <div class="flex items-center space-x-4">
            <input type="search" placeholder="Search..."
                class="px-3 py-2 rounded-lg border bg-purple-800 text-white" />
            <div class="flex items-center space-x-3 cursor-pointer select-none">
                <div
                    class="w-10 h-10 rounded-full bg-purple-700 flex items-center justify-center font-bold text-white">
                    <?= htmlspecialchars(substr($nama_mentor, 0, 1)) ?></div>
                <div class="text-white font-medium whitespace-nowrap"><?= htmlspecialchars($nama_mentor) ?></div>
            </div>
        </div>
    </header>

    <main class="flex-1 ml-64 p-10">
        <h2 class="text-2xl font-semibold text-gray-800 mb-8">CRUD Materi</h2>

        <button id="openCreateModalBtn"
            class="mb-6 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">Tambah
            Materi</button>

        <!-- Modal Create -->
        <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div
                class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button id="closeCreateModalBtn"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
                <h3 class="text-xl font-semibold mb-4">Tambah Materi Baru</h3>
                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="id_materi" value="">
                    <div>
                        <label class="block font-medium mb-1">Judul Materi</label>
                        <input name="judul" type="text" required class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Divisi</label>
                        <input type="hidden" name="divisi_id" value="<?= $id_divisi ?>" />
                        <input type="text" value="<?= htmlspecialchars($divisi_mentor_nama) ?>" disabled
                            class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Upload Video (wajib diisi)</label>
                        <input name="file_url" type="file" accept="video/*" required
                            class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">Tambah
                        Materi</button>
                </form>
            </div>
        </div>

        <!-- Modal Update -->
        <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <button id="closeUpdateModalBtn"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
                <h3 class="text-xl font-semibold mb-4">Edit Materi</h3>
                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="text" name="id_materi" value="">
                    <div>
                        <label class="block font-medium mb-1">Judul Materi</label>
                        <input name="judul" type="text" required class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Divisi</label>
                        <input type="hidden" name="divisi_id" value="<?= $id_divisi ?>" />
                        <input type="text" value="<?= htmlspecialchars($divisi_mentor_nama) ?>" disabled
                            class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Upload Video (biarkan kosong jika tidak ingin ganti)</label>
                        <input name="file_url" type="file" accept="video/*"
                            class="w-full border rounded-md px-3 py-2" />
                    </div>
                    <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">Update
                        Materi</button>
                </form>
            </div>
        </div>

        <section class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-6">Daftar Materi</h3>
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2 text-left">Judul</th>
                        <th class="border px-4 py-2 text-left">Divisi</th>
                        <th class="border px-4 py-2 text-left">Tanggal Upload</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materiList as $materi): ?>
                        <tr class="hover:bg-purple-50">
                            <td class="border px-4 py-2"><?= htmlspecialchars($materi['judul']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($materi['nama_divisi']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($materi['tanggal_upload']) ?></td>
                            <td class="border px-4 py-2 text-center space-x-2">
                                <a href="#" class="editBtn text-blue-600 hover:text-blue-800 font-semibold"
                                    data-id="<?= (int)$materi['id_materi'] ?>"
                                    data-judul="<?= htmlspecialchars($materi['judul']) ?>"
                                    data-divisi="<?= (int)$materi['divisi_id'] ?>">Edit</a>
                                <a href="?delete=<?= htmlspecialchars($materi['id_materi']) ?>"
                                    onclick="return confirm('Yakin ingin menghapus materi ini?')"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        // Modal create
        const createModal = document.getElementById('createModal');
        const openCreateBtn = document.getElementById('openCreateModalBtn');
        const closeCreateBtn = document.getElementById('closeCreateModalBtn');
        const createForm = createModal.querySelector('form');

        openCreateBtn.addEventListener('click', () => {
            createForm.reset();
            createModal.classList.remove('hidden');
        });

        closeCreateBtn.addEventListener('click', () => {
            createModal.classList.add('hidden');
            createForm.reset();
        });

        // Modal update
        const updateModal = document.getElementById('updateModal');
        const closeUpdateBtn = document.getElementById('closeUpdateModalBtn');
        const updateForm = updateModal.querySelector('form');

        closeUpdateBtn.addEventListener('click', () => {
            updateModal.classList.add('hidden');
            updateForm.reset();
        });

        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', e => {
                e.preventDefault();

                const id = button.dataset.id; // id_materi
                const judul = button.dataset.judul; // judul materi
                // const divisi = button.dataset.divisi; // gak perlu set ulang divisi

                // Masukkan ke form update
                updateForm.querySelector('input[name="id_materi"]').value = id;
                updateForm.querySelector('input[name="judul"]').value = judul;

                // Buka modal update
                updateModal.classList.remove('hidden');
            });
        });

        // Close modals if click outside content
        window.addEventListener('click', e => {
            if (e.target === createModal) {
                createModal.classList.add('hidden');
                createForm.reset();
            }
            if (e.target === updateModal) {
                updateModal.classList.add('hidden');
                updateForm.reset();
            }
        });
    </script>
</body>

</html>