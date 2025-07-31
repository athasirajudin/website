<?php
session_start();
require_once '../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$berita_data = null; // Variabel untuk menyimpan data berita yang akan diedit
$success_message = '';
$error_message = '';
$id_berita = $_GET['id'] ?? null; // Ambil ID berita dari URL

// --- Bagian Pengambilan Data Berita untuk Form ---
if ($id_berita) {
    try {
        $stmt = $pdo->prepare("SELECT id, judul, isi, gambar, penulis FROM berita WHERE id = :id");
        $stmt->bindParam(':id', $id_berita);
        $stmt->execute();
        $berita_data = $stmt->fetch();

        if (!$berita_data) {
            $error_message = 'Berita tidak ditemukan.';
            // Redirect ke dashboard jika berita tidak ditemukan
            header('Location: index.php?status=error&message=' . urlencode('Berita tidak ditemukan.'));
            exit();
        }
    } catch (PDOException $e) {
        $error_message = 'Terjadi kesalahan database saat mengambil data berita: ' . $e->getMessage();
    }
} else {
    $error_message = 'ID berita tidak disediakan.';
    // Redirect ke dashboard jika ID tidak ada
    header('Location: index.php?status=error&message=' . urlencode('ID berita tidak disediakan.'));
    exit();
}

// --- Bagian Penanganan Update Form ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_berita = $_POST['id_berita'] ?? null; // Ambil ID dari hidden field
    $judul_baru = $_POST['judul'] ?? '';
    $isi_baru = $_POST['isi'] ?? '';
    $penulis_baru = $_POST['penulis'] ?? '';
    $gambar_lama_nama = $berita_data['gambar'] ?? null; // Nama gambar lama dari database
    $gambar_baru_nama = $gambar_lama_nama; // Defaultnya tetap pakai gambar lama

    // Validasi input
    if (empty($judul_baru) || empty($isi_baru) || empty($penulis_baru)) {
        $error_message = 'Semua field (Judul, Isi, Penulis) harus diisi.';
    } else if (!$id_berita) {
        $error_message = 'ID berita untuk update tidak valid.';
    } else {
        // --- Penanganan Upload Gambar Baru ---
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../assets/img/";
            $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar_baru_nama = uniqid('berita_') . '.' . $file_extension;
            $target_file = $target_dir . $gambar_baru_nama;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['gambar']['tmp_name']);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $error_message = "File bukan gambar.";
                $uploadOk = 0;
            }

            if ($_FILES['gambar']['size'] > 2000000000) { // 2GB
                $error_message = "Maaf, ukuran gambar terlalu besar. Maksimal 2MB.";
                $uploadOk = 0;
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $error_message = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                 // Jika ada error upload, pastikan nama gambar kembali ke yang lama atau null
                $gambar_baru_nama = $gambar_lama_nama;
            } else {
                // Hapus gambar lama jika ada dan gambar baru berhasil diupload
                if ($gambar_lama_nama && file_exists($target_dir . $gambar_lama_nama)) {
                    unlink($target_dir . $gambar_lama_nama);
                }
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                    $error_message = "Terjadi kesalahan saat mengunggah gambar baru.";
                    $gambar_baru_nama = $gambar_lama_nama; // Kembali ke gambar lama jika gagal upload
                }
            }
        } // End of file upload handling

        // Jika tidak ada error dari upload gambar atau validasi awal
        if (empty($error_message)) {
            try {
                // Siapkan query UPDATE
                $stmt = $pdo->prepare("UPDATE berita SET judul = :judul, isi = :isi, gambar = :gambar, penulis = :penulis WHERE id = :id");

                // Bind parameter
                $stmt->bindParam(':judul', $judul_baru);
                $stmt->bindParam(':isi', $isi_baru);
                $stmt->bindParam(':gambar', $gambar_baru_nama);
                $stmt->bindParam(':penulis', $penulis_baru);
                $stmt->bindParam(':id', $id_berita);

                // Eksekusi query
                if ($stmt->execute()) {
                    $success_message = 'Berita berhasil diperbarui!';
                    // Update berita_data agar form menampilkan data terbaru
                    $berita_data['judul'] = $judul_baru;
                    $berita_data['isi'] = $isi_baru;
                    $berita_data['gambar'] = $gambar_baru_nama;
                    $berita_data['penulis'] = $penulis_baru;
                    // Redirect ke dashboard admin dengan pesan sukses
                    header('Location: index.php?status=success&message=' . urlencode('Berita berhasil diperbarui!'));
                    exit();
                } else {
                    $error_message = 'Gagal memperbarui berita.';
                }
            } catch (PDOException $e) {
                $error_message = 'Terjadi kesalahan database saat memperbarui: ' . $e->getMessage();
                // Jika ada error database setelah upload gambar baru, hapus gambar baru
                if ($gambar_baru_nama != $gambar_lama_nama && file_exists($target_dir . $gambar_baru_nama)) {
                    unlink($target_dir . $gambar_baru_nama);
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
    <title>Edit Berita - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        .navbar-shadow {
            box-shadow: 0 2px 4px rgba
    (0,0,0,0.1);
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
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Edit Berita</h1>

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

            <?php if ($berita_data): // Tampilkan form hanya jika data berita ditemukan ?>
            <form action="edit_berita.php?id=<?php echo htmlspecialchars($id_berita); ?>" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl mx-auto">
                <input type="hidden" name="id_berita" value="<?php echo htmlspecialchars($berita_data['id']); ?>">

                <div>
                    <label for="judul" class="block text-gray-700 text-sm font-bold mb-2">Judul Berita:</label>
                    <input type="text" id="judul" name="judul" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($berita_data['judul']); ?>" required>
                </div>

                <div>
                    <label for="isi" class="block text-gray-700 text-sm font-bold mb-2">Isi Berita:</label>
                    <textarea id="isi" name="isi" rows="10" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required><?php echo htmlspecialchars($berita_data['isi']); ?></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Saat Ini:</label>
                    <div class="mb-4">
                        <?php if ($berita_data['gambar'] && file_exists('../assets/img/' . $berita_data['gambar'])): ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($berita_data['gambar']); ?>" alt="Gambar Berita Lama" class="rounded-lg shadow-md max-w-xs h-auto object-cover">
                            <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($berita_data['gambar']); ?></p>
                        <?php else: ?>
                            <p class="text-gray-500">Tidak ada gambar saat ini.</p>
                        <?php endif; ?>
                    </div>
                    <label for="gambar" class="block text-gray-700 text-sm font-bold mb-2">Ganti Gambar (Opsional):</label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" class="block w-full text-sm text-gray-700
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 cursor-pointer">
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah gambar. Maks. ukuran file 2MB. Format: JPG, PNG, GIF.</p>
                </div>

                <div>
                    <label for="penulis" class="block text-gray-700 text-sm font-bold mb-2">Penulis:</label>
                    <input type="text" id="penulis" name="penulis" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($berita_data['penulis']); ?>" required>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Update Berita</button>
                </div>
            </form>
            <?php endif; ?>
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
