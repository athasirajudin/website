<?php
session_start();
require_once 'config/koneksi.php'; // Path koneksi dari root

// Anda bisa menambahkan logika PHP di sini jika ada data profil sekolah yang dinamis
// Misalnya, mengambil data kepala sekolah dari database jika ada tabel terpisah
// Saat ini, data kepala sekolah akan statis di HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah - Website Profil Sekolah</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f8f8; /* General background color */
        }
        .navbar-shadow {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Header/Navbar -->
    <header class="bg-white p-4 navbar-shadow sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-700">Nama Sekolah</a>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-blue-700 font-medium">Beranda</a>
                <a href="profil.php" class="text-blue-700 font-medium border-b-2 border-blue-700 pb-1">Profil Sekolah</a>
                <a href="berita.php" class="text-gray-700 hover:text-blue-700 font-medium">Berita</a>
                <a href="kontak.php" class="text-gray-700 hover:text-blue-700 font-medium">Kontak</a>
                <a href="admin/login.php" class="text-gray-700 hover:text-blue-700 font-medium">Admin</a>
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
            <a href="index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Beranda</a>
            <a href="profil.php" class="block px-4 py-2 text-blue-700 bg-gray-100">Profil Sekolah</a>
            <a href="berita.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Berita</a>
            <a href="kontak.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Kontak</a>
            <a href="admin/login.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Admin</a>
        </div>
    </header>

    <!-- Main Content - Profil Sekolah -->
    <main class="flex-grow container mx-auto p-6 md:p-8">
        <section class="bg-white rounded-lg shadow-md p-8 my-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-6 text-center">Profil Sekolah Kami</h1>

            <!-- Bagian Visi & Misi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 items-center">
                <div>
                    <img src="assets/img/gedung_64.jpg" alt="Visi Misi" class="rounded-lg shadow-md w-full h-auto object-cover">
                </div>
                <div>
                    <h2 class="text-3xl font-semibold text-blue-700 mb-4">Visi</h2>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Memiliki tamatan yang Berbudi pekerti luhur, Berkarakter, Mandiri, Berprestasi dan Berjiwa Wirausaha.
                    </p>
                    <h2 class="text-3xl font-semibold text-blue-700 mb-4">Misi</h2>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>Mengimplementasikan 5S (Senyum, Sapa, Salam, Sopan dan Santun).</li>
                        <li>Membangun peserta didik menjadi seseorang yang memiliki sikap profesional.</li>
                        <li>Mengarahkan peserta didik untuk meningkatkan potensi dan keahlian diri melalui pelatihan di dalam maupun di luar lingkungan sekolah</li>
                        <li>Menyiapkan tamatan agar mendapatkan prestasi juara di tingkat nasional dengan pelatihan di setiap kompetensi.</li>
                        <li>Mengarahkan peserta didik mempunyai jiwa wirausaha melalui pelajaran kewirausahaan.</li>
                    </ul>
                </div>
            </div>

            <hr class="my-10 border-gray-200">

            <!-- Bagian Foto Kepala Sekolah -->
            <div class="text-center my-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Sambutan Kepala Sekolah</h2>
                <div class="max-w-xs mx-auto mb-6">
                    <img src="assets/img/kepala_sekolah.jpeg" alt="Foto Kepala Sekolah" class="rounded-full shadow-lg w-full h-auto object-cover border-4 border-blue-200">
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Dewi Puspitasari, S.ST.Par, M.Par</h3>
                <p class="text-blue-600 font-medium mb-4">Kepala Sekolah SMKN 64 JAKARTA TIMUR</p>
                <p class="text-gray-700 leading-relaxed max-w-2xl mx-auto">
                    "Sebagai lembaga pendidikan, SMKN 64 Jakarta tanggap dengan perkembangan teknologi tersebut. Dengan dukungan SDM yang dimiliki, sekolah ini siap untuk berkompetisi dengan sekolah lain dalam pelayanan informasi publik. Teknologi Informasi Web khususnya, menjadi sarana bagi SMK Negeri 64 Jakarta untuk memberi pelayanan informasi secara cepat, jelas, dan akuntabel. Dari layanan ini pula, sekolah siap menerima saran dari semua pihak yang akhirnya dapat menjawab Kebutuhan masyarakat."
                </p>
            </div>

            <hr class="my-10 border-gray-200">

            <!-- Bagian Sejarah Singkat -->
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sejarah Singkat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 items-start">
                <div>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Sekolah kami didirikan pada tahun 19XX dengan tujuan mulia untuk memberikan akses pendidikan yang merata dan berkualitas bagi seluruh lapisan masyarakat. Berawal dari sebuah bangunan sederhana, kami terus berkembang seiring waktu, beradaptasi dengan perubahan zaman dan kebutuhan pendidikan.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Selama bertahun-tahun, kami telah meluluskan ribuan alumni yang sukses di berbagai bidang, membuktikan komitmen kami terhadap keunggulan pendidikan. Setiap langkah perjalanan kami didasari oleh semangat untuk terus berinovasi dan memberikan yang terbaik bagi para siswa.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Kami bangga dengan sejarah kami dan bersemangat untuk terus menulis babak baru dalam dunia pendidikan, mencetak generasi penerus bangsa yang siap menghadapi tantangan global.
                    </p>
                </div>
                <div>
                    <img src="https://placehold.co/600x400/FFD700/000000?text=Sejarah+Sekolah" alt="Sejarah Sekolah" class="rounded-lg shadow-md w-full h-auto object-cover">
                </div>
            </div>

            <hr class="my-10 border-gray-200">

            <!-- Bagian Fasilitas Sekolah -->
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Fasilitas Sekolah</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm text-center">
                    <img src="https://placehold.co/100x100/AEC6CF/000000?text=Perpustakaan" alt="Perpustakaan" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Perpustakaan Modern</h3>
                    <p class="text-gray-600">Koleksi buku lengkap dan area belajar yang nyaman.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm text-center">
                    <img src="https://placehold.co/100x100/AEC6CF/000000?text=Lab+Komputer" alt="Laboratorium Komputer" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Laboratorium Komputer</h3>
                    <p class="text-gray-600">Dilengkapi dengan perangkat keras dan lunak terbaru.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm text-center">
                    <img src="https://placehold.co/100x100/AEC6CF/000000?text=Lapangan+Olahraga" alt="Lapangan Olahraga" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Lapangan Olahraga</h3>
                    <p class="text-gray-600">Fasilitas lengkap untuk berbagai kegiatan olahraga.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm text-center">
                    <img src="https://placehold.co/100x100/AEC6CF/000000?text=Ruang+Seni" alt="Ruang Seni" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Ruang Seni & Musik</h3>
                    <p class="text-gray-600">Mendukung pengembangan bakat artistik siswa.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm text-center">
                    <img src="https://placehold.co/100x100/AEC6CF/000000?text=Kantin" alt="Kantin Sehat" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Kantin Sehat</h3>
                    <p class="text-gray-600">Menyediakan makanan bergizi dan kebersihan terjamin.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow-sm text-center">
                    <img src="https://placehold.co/100x100/AEC6CF/000000?text=Musholla" alt="Musholla" class="mx-auto mb-4 rounded-full">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Musholla</h3>
                    <p class="text-gray-600">Fasilitas ibadah yang nyaman bagi seluruh warga sekolah.</p>
                </div>
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
