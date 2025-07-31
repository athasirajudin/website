<?php
session_start(); // Selalu mulai sesi di awal setiap halaman yang membutuhkan sesi

// Sertakan file koneksi database
require_once '../config/koneksi.php'; // Sesuaikan path jika berbeda

$error_message = ''; // Variabel untuk menyimpan pesan error

// Periksa apakah form login telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password)) {
        $error_message = 'Username dan password tidak boleh kosong.';
    } else {
        try {
            // Siapkan query untuk mengambil data user berdasarkan username
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch();

            // Periksa apakah user ditemukan dan password cocok
            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect ke halaman dashboard admin
                header('Location: index.php');
                exit();
            } else {
                // Login gagal
                $error_message = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            $error_message = 'Terjadi kesalahan database: ' . $e->getMessage();
        }
    }
}

// Jika user sudah login, redirect ke dashboard admin (mencegah akses langsung ke login page)
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Website Profil Sekolah</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5; /* Warna latar belakang yang lebih terang untuk halaman login */
        }
        .navbar-shadow {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Header/Navbar (Optional for admin login page, but kept for consistency) -->
    <header class="bg-white p-4 navbar-shadow sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-blue-700">Nama Sekolah</a>
            <div class="hidden md:flex space-x-6">
                <a href="../index.php" class="text-gray-700 hover:text-blue-700 font-medium">Beranda</a>
                <a href="../profil.php" class="text-gray-700 hover:text-blue-700 font-medium">Profil Sekolah</a>
                <a href="../berita.php" class="text-gray-700 hover:text-blue-700 font-medium">Berita</a>
                <a href="../kontak.php" class="text-gray-700 hover:text-blue-700 font-medium">Kontak</a>
                <a href="login.php" class="text-blue-700 font-medium border-b-2 border-blue-700 pb-1">Admin</a>
            </div>
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </nav>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white mt-2 py-2 rounded-lg shadow-lg">
            <a href="../index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Beranda</a>
            <a href="../profil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profil Sekolah</a>
            <a href="../berita.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Berita</a>
            <a href="../kontak.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Kontak</a>
            <a href="login.php" class="block px-4 py-2 text-blue-700 bg-gray-100">Admin</a>
        </div>
    </header>

    <!-- Main Content - Login Admin -->
    <main class="flex-grow flex items-center justify-center p-6 md:p-8">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Login Admin</h1>
            <p class="text-center text-gray-600 mb-8">Masukkan kredensial Anda untuk mengakses dashboard admin.</p>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                    <input type="text" id="username" name="username" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan username" required>
                </div>
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan password" required>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Login</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-6 text-center mt-auto">
        <div class="container mx-auto">
            <p>&copy; 2025 Nama Sekolah. Hak Cipta Dilindungi Undang-Undang.</p>
            <p class="text-sm mt-2">Dibuat untuk Ujian Kompetensi Keahlian RPL.</p>
        </div>
    </footer>

    <script>
        // JavaScript for mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside (optional, but good UX)
        document.addEventListener('click', (event) => {
            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
