<?php
session_start();
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = login($_POST['email'], $_POST['password']);
    if ($role) {
        if ($role == 'anggota') {
            header("Location: ../../frontend/main/index.html");
        } else if ($role == 'mentor') {
            header("Location: mentor/index.php");
        } else if ($role == 'admin') {
            header("Location: admin/index.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f1f5ff] flex items-center justify-center min-h-screen font-[Segoe UI] text-[#333]">
    <div class="w-full max-w-md p-5">
        <div class="bg-white p-8 rounded-xl shadow-lg text-center">
            <h2 class="text-[#7b2cbf] text-2xl font-semibold mb-2">Login</h2>
            <p class="text-[#555] mb-5">Silakan masuk untuk melanjutkan</p>
            <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    Registrasi Berhasil! Silahkan Login.
                </div>
            <?php endif; ?>
            <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

            <form method="POST" class="space-y-4 text-left">
                <input type="email" name="email" placeholder="Email" required
                    class="w-full p-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#7b2cbf]">
                <input type="password" name="password" placeholder="Password" required
                    class="w-full p-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#7b2cbf]">
                <button type="submit"
                    class="w-full bg-[#7b2cbf] text-white py-3 rounded-md text-base hover:bg-[#6930c3] transition duration-300">
                    Login
                </button>
            </form>

            <div class="hotspot-info text-sm text-[#555] mt-4">
                Belum punya akun? <a href="register_page.php" class="text-[#7b2cbf] hover:underline">Daftar di sini</a>
            </div>
        </div>
    </div>
</body>

</html>