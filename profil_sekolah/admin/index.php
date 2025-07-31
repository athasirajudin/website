<?php
session_start(); // Selalu mulai sesi di awal setiap halaman yang membutuhkan sesi

// Sertakan file koneksi database
require_once '../config/koneksi.php'; // Sesuaikan path jika berbeda

// --- Bagian Autentikasi Admin ---
// Periksa apakah pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jika belum login, redirect ke halaman login
    header('Location: login.php'); // Pastikan ini mengarah ke file login.php Anda
    exit();
}

$success_message = ''; // Variabel untuk menyimpan pesan sukses
$error_message = '';   // Variabel untuk menyimpan pesan error

// --- Bagian Penanganan Operasi Hapus Berita ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_berita = $_POST['id'] ?? null;

    if ($id_berita) {
        try {
            // Opsional: Ambil nama file gambar terlebih dahulu untuk dihapus dari server
            $stmt_select_img = $pdo->prepare("SELECT gambar FROM berita WHERE id = :id");
            $stmt_select_img->bindParam(':id', $id_berita);
            $stmt_select_img->execute();
            $gambar_lama = $stmt_select_img->fetchColumn();

            // Siapkan query untuk menghapus berita
            $stmt_delete = $pdo->prepare("DELETE FROM berita WHERE id = :id");
            $stmt_delete->bindParam(':id', $id_berita);

            if ($stmt_delete->execute()) {
                // Jika penghapusan dari DB berhasil, hapus file gambar jika ada
                // Pastikan path ke folder gambar benar
                if ($gambar_lama && file_exists('../assets/img/' . $gambar_lama)) {
                    unlink('../assets/img/' . $gambar_lama);
                }
                $success_message = 'Berita berhasil dihapus.';
            } else {
                $error_message = 'Gagal menghapus berita.';
            }
        } catch (PDOException $e) {
            $error_message = 'Terjadi kesalahan database saat menghapus: ' . $e->getMessage();
        }
    } else {
        $error_message = 'ID berita tidak valid.';
    }
}

// --- Bagian Pengambilan Data Berita (untuk ditampilkan di tabel) ---
$berita = []; // Inisialisasi array untuk menampung berita
try {
    $stmt = $pdo->query("SELECT id, judul, tanggal_publikasi, penulis FROM berita ORDER BY tanggal_publikasi DESC");
    $berita = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = 'Error mengambil data berita: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Website Profil Sekolah</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        .navbar-shadow {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-auto {
            width: 100%;
            border-collapse: collapse;
        }
        .table-auto th, .table-auto td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0; /* Tailwind's gray-200 */
        }
        .table-auto th {
            background-color: #edf2f7; /* Tailwind's gray-100 */
            font-weight: 600;
            color: #2d3748; /* Tailwind's gray-800 */
        }
        .table-auto tbody tr:hover {
            background-color: #f7fafc; /* Tailwind's gray-50 */
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Header/Navbar -->
    <header class="bg-white p-4 navbar-shadow sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-blue-700">Nama Sekolah</a>
            <div class="hidden md:flex space-x-6">
                <a href="../index.php" class="text-gray-700 hover:text-blue-700 font-medium">Beranda</a>
                <a href="../profil.php" class="text-gray-700 hover:text-blue-700 font-medium">Profil Sekolah</a>
                <a href="../berita.php" class="text-gray-700 hover:text-blue-700 font-medium">Berita</a>
                <a href="../kontak.php" class="text-gray-700 hover:text-blue-700 font-medium">Kontak</a>
                <a href="index.php" class="text-blue-700 font-medium border-b-2 border-blue-700 pb-1">Admin</a>
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
            <a href="index.php" class="block px-4 py-2 text-blue-700 bg-gray-100">Admin</a>
        </div>
    </header>

    <!-- Main Content - Dashboard Admin -->
    <main class="flex-grow container mx-auto p-6 md:p-8">
        <section class="bg-white rounded-lg shadow-md p-8 my-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
                <a href="tambah_berita.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                    + Tambah Berita
                </a>
            </div>

            <p class="text-gray-700 mb-6">Selamat datang, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>! Anda dapat mengelola berita sekolah di sini.</p>

            <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Tabel Daftar Berita -->
            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                <table class="min-w-full bg-white table-auto">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-gray-600">No.</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-gray-600">Judul Berita</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-gray-600">Tanggal Publikasi</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-gray-600">Penulis</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($berita) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($berita as $item): ?>
                                <tr>
                                    <td class="py-3 px-4 text-gray-700"><?php echo $no++; ?></td>
                                    <td class="py-3 px-4 text-gray-700 font-medium"><?php echo htmlspecialchars($item['judul']); ?></td>
                                    <td class="py-3 px-4 text-gray-700"><?php echo date('d F Y', strtotime($item['tanggal_publikasi'])); ?></td>
                                    <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($item['penulis']); ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="edit_berita.php?id=<?php echo $item['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold py-2 px-3 rounded-lg mr-2 transition duration-200">Edit</a>
                                        <form action="index.php" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-2 px-3 rounded-lg transition duration-200">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-3 px-4 text-center text-gray-500">Belum ada berita yang ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tombol Logout -->
            <div class="mt-8 text-right">
                <a href="logout.php" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105">Logout</a>
            </div>
        </section>
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
