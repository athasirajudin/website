<?php
session_start();
require_once '../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$success_message = '';
$error_message = '';

// Tangani submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'] ?? '';
    $isi = $_POST['isi'] ?? '';
    $penulis = $_POST['penulis'] ?? '';
    $gambar_nama = null; // Default tidak ada gambar

    // Validasi input
    if (empty($judul) || empty($isi) || empty($penulis)) {
        $error_message = 'Semua field (Judul, Isi, Penulis) harus diisi.';
    } else {
        // --- Penanganan Upload Gambar ---
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../assets/img/"; // Folder tempat menyimpan gambar
            $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar_nama = uniqid('berita_') . '.' . $file_extension; // Nama unik untuk gambar
            $target_file = $target_dir . $gambar_nama;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek apakah file adalah gambar asli atau fake
            $check = getimagesize($_FILES['gambar']['tmp_name']);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $error_message = "File bukan gambar.";
                $uploadOk = 0;
            }

            // Batasi ukuran file (misal: 2MB)
            if ($_FILES['gambar']['size'] > 2000000) {
                $error_message = "Maaf, ukuran gambar terlalu besar. Maksimal 2MB.";
                $uploadOk = 0;
            }

            // Izinkan format file tertentu
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $error_message = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
                $uploadOk = 0;
            }

            // Jika semua validasi ok, coba upload file
            if ($uploadOk == 0) {
                $error_message = ($error_message == '') ? "Maaf, gambar tidak berhasil diunggah." : $error_message;
            } else {
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $error_message = "Terjadi kesalahan saat mengunggah gambar.";
                    $gambar_nama = null; // Pastikan tidak ada nama gambar yang disimpan jika upload gagal
                }
            }
        }

        // Jika tidak ada error dari upload gambar atau validasi awal
        if (empty($error_message)) {
            try {
                // Siapkan query INSERT
                $stmt = $pdo->prepare("INSERT INTO berita (judul, isi, gambar, penulis) VALUES (:judul, :isi, :gambar, :penulis)");

                // Bind parameter
                $stmt->bindParam(':judul', $judul);
                $stmt->bindParam(':isi', $isi);
                $stmt->bindParam(':gambar', $gambar_nama);
                $stmt->bindParam(':penulis', $penulis);

                // Eksekusi query
                if ($stmt->execute()) {
                    $success_message = 'Berita berhasil ditambahkan!';
                    // Reset form setelah sukses (opsional)
                    $judul = '';
                    $isi = '';
                    $penulis = '';
                    // Atau redirect ke halaman dashboard admin
                    header('Location: index.php?status=success&message=' . urlencode('Berita berhasil ditambahkan!'));
                    exit();
                } else {
                    $error_message = 'Gagal menambahkan berita.';
                }
            } catch (PDOException $e) {
                $error_message = 'Terjadi kesalahan database: ' . $e->getMessage();
                // Jika ada error database setelah upload gambar, hapus gambar yang sudah terupload
                if ($gambar_nama && file_exists($target_file)) {
                    unlink($target_file);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita - Admin</title>
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
    </style>
</head>
<body class="flex flex-col min-h-screen">
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
            <button id="mobile-menu-button" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-white mt-2 py-2 rounded-lg shadow-lg">
            <a href="../index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Beranda</a>
            <a href="../profil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profil Sekolah</a>
            <a href="../berita.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Berita</a>
            <a href="../kontak.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Kontak</a>
            <a href="index.php" class="block px-4 py-2 text-blue-700 bg-gray-100">Admin</a>
        </div>
    </header>

    <main class="flex-grow container mx-auto p-6 md:p-8">
        <section class="bg-white rounded-lg shadow-md p-8 my-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Tambah Berita Baru</h1>

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

            <form action="tambah_berita.php" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl mx-auto">
                <div>
                    <label for="judul" class="block text-gray-700 text-sm font-bold mb-2">Judul Berita:</label>
                    <input type="text" id="judul" name="judul" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan judul berita" value="<?php echo htmlspecialchars($judul ?? ''); ?>" required>
                </div>

                <div>
                    <label for="isi" class="block text-gray-700 text-sm font-bold mb-2">Isi Berita:</label>
                    <textarea id="isi" name="isi" rows="10" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tulis isi berita di sini..." required><?php echo htmlspecialchars($isi ?? ''); ?></textarea>
                </div>

                <div>
                    <label for="gambar" class="block text-gray-700 text-sm font-bold mb-2">Gambar Berita (Opsional):</label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" class="block w-full text-sm text-gray-700
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 cursor-pointer">
                    <p class="mt-1 text-sm text-gray-500">Maks. ukuran file 2MB. Format: JPG, PNG, GIF.</p>
                </div>

                <div>
                    <label for="penulis" class="block text-gray-700 text-sm font-bold mb-2">Penulis:</label>
                    <input type="text" id="penulis" name="penulis" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nama penulis" value="<?php echo htmlspecialchars($penulis ?? ''); ?>" required>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Simpan Berita</button>
                </div>
            </form>
        </section>
    </main>

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
