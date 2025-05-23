<?php
session_start();
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    $result = register($name, $email, $password, $confirmPassword);

    if ($result === true) {
        header("Location: login_page.php?register=success");
        echo $result;
        exit;
    } else {
        $server = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Hotspot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f1f5ff] flex items-center justify-center min-h-screen font-[Segoe UI] text-[#333]">
    <div class="w-full max-w-md p-5">
        <div class="bg-white p-8 rounded-xl shadow-lg text-center animate-fade-in">
            <h2 class="text-[#7b2cbf] text-2xl font-semibold mb-2">Register</h2>
            <p class="text-[#555] mb-5">Buat akun baru untuk login ke sistem</p>

            <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

            <form method="POST" class="space-y-4 text-left">
                <input type="text" name="name" placeholder="Nama Lengkap" required
                    class="w-full p-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#7b2cbf] hover:border-[#7b2cbf] transition duration-200">
                <input type="email" name="email" placeholder="Email" required
                    class="w-full p-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#7b2cbf] hover:border-[#7b2cbf] transition duration-200">
                <input type="password" name="password" placeholder="Password" required
                    class="w-full p-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#7b2cbf] hover:border-[#7b2cbf] transition duration-200">
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required
                    class="w-full p-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#7b2cbf] hover:border-[#7b2cbf] transition duration-200">
                <button type="submit"
                    class="w-full bg-[#7b2cbf] text-white py-3 rounded-md text-base transform hover:scale-105 hover:bg-[#6930c3] transition duration-300">
                    Register
                </button>
            </form>

            <div class="text-sm text-[#555] mt-4">
                Sudah punya akun?
                <a href="login_page.php"
                    class="text-[#7b2cbf] relative inline-block after:content-[''] after:absolute after:w-0 after:h-[2px] after:bg-[#7b2cbf] after:left-0 after:bottom-0 hover:after:w-full after:transition-all after:duration-300">
                    Login di sini
                </a>
            </div>
        </div>
    </div>

    <!-- Custom Tailwind animation -->
    <style>
        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
        }
    </style>
</body>

</html>