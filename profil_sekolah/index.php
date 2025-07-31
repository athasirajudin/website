<?php
session_start();
require_once 'config/koneksi.php'; // Path koneksi dari root

$latest_berita = []; // Variabel untuk menyimpan berita terbaru
$error_message = '';

try {
    // Ambil 3 berita terbaru dari database
    $stmt = $pdo->query("SELECT id, judul, isi, gambar, tanggal_publikasi, penulis FROM berita ORDER BY tanggal_publikasi DESC LIMIT 3");
    $latest_berita = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Terjadi kesalahan saat mengambil berita terbaru: " . $e->getMessage();
}

// Fungsi untuk membatasi panjang teks dan menambahkan "..."
function truncateText($text, $limit) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Website Profil Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f8f8;
        }
        .navbar-shadow {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="bg-white p-4 navbar-shadow sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-700">Nama Sekolah</a>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="text-blue-700 font-medium border-b-2 border-blue-700 pb-1">Beranda</a>
                <a href="profil.php" class="text-gray-700 hover:text-blue-700 font-medium">Profil Sekolah</a>
                <a href="berita.php" class="text-gray-700 hover:text-blue-700 font-medium">Berita</a>
                <a href="kontak.php" class="text-gray-700 hover:text-blue-700 font-medium">Kontak</a>
                <a href="admin/login.php" class="text-gray-700 hover:text-blue-700 font-medium">Admin</a>
            </div>
            <button id="mobile-menu-button" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-white mt-2 py-2 rounded-lg shadow-lg">
            <a href="index.php" class="block px-4 py-2 text-blue-700 bg-gray-100">Beranda</a>
            <a href="profil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profil Sekolah</a>
            <a href="berita.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Berita</a>
            <a href="kontak.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Kontak</a>
            <a href="admin/login.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Admin</a>
        </div>
    </header>

    <section class="bg-blue-600 text-white py-16 px-6 text-center">
        <div class="container mx-auto">
            <h1 class="text-5xl font-extrabold mb-4 animate-fade-in-down">Selamat Datang di Website Profil Sekolah Kami</h1>
            <p class="text-xl mb-8 animate-fade-in-up">Mendidik Generasi Penerus Bangsa dengan Dedikasi dan Inovasi.</p>
            <a href="profil.php" class="bg-white text-blue-600 hover:bg-blue-100 font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 inline-block animate-scale-in">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </section>

    <section class="container mx-auto p-6 md:p-8 my-8 bg-white rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Tentang Sekolah Kami</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Sekolah kami didirikan dengan visi untuk menciptakan lingkungan belajar yang inspiratif dan transformatif. Kami berkomitmen untuk menyediakan pendidikan berkualitas tinggi yang tidak hanya berfokus pada keunggulan akademik, tetapi juga pada pengembangan karakter, keterampilan sosial, dan kreativitas siswa.
        </p>
        <p class="text-gray-700 leading-relaxed">
            Dengan fasilitas modern, kurikulum inovatif, dan tim pengajar berdedikasi, kami berupaya mempersiapkan setiap siswa untuk menjadi individu yang mandiri, bertanggung jawab, dan siap menghadapi tantangan masa depan.
        </p>
        <div class="flex justify-center mt-8">
            <a href="profil.php" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                Lihat Profil Lengkap
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </section>

    <section class="container mx-auto p-6 md:p-8 my-8 bg-white rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Berita Terbaru</h2>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (count($latest_berita) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($latest_berita as $item): ?>
                    <div class="bg-gray-50 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <?php if ($item['gambar'] && file_exists('assets/img/' . $item['gambar'])): ?>
                            <img src="assets/img/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['judul']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <img src="https://placehold.co/400x200/cccccc/333333?text=No+Image" alt="No Image" class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($item['judul']); ?></h3>
                            <p class="text-gray-600 text-sm mb-3">
                                <span class="font-medium"><?php echo htmlspecialchars($item['penulis']); ?></span> |
                                <?php echo date('d F Y', strtotime($item['tanggal_publikasi'])); ?>
                            </p>
                            <p class="text-gray-700 mb-4"><?php echo htmlspecialchars(truncateText($item['isi'], 100)); ?></p>
                            </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="flex justify-center mt-8">
                <a href="berita.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    Lihat Semua Berita
                </a>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 text-lg">Belum ada berita terbaru saat ini.</p>
        <?php endif; ?>
    </section>

    <section class="bg-blue-700 text-white py-12 px-6 text-center my-8 rounded-lg shadow-md">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold mb-4">Ingin Bergabung dengan Kami?</h2>
            <p class="text-lg mb-6">Daftar sekarang dan jadilah bagian dari komunitas belajar kami yang dinamis!</p>
            <a href="kontak.php" class="bg-white text-blue-700 hover:bg-blue-100 font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 inline-block">
                Daftar Sekarang
            </a>
        </div>
    </section>

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
