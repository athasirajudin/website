<?php
// Pengaturan koneksi database
$host = 'localhost'; // Biasanya localhost untuk Laragon/XAMPP
$dbname = 'db_sekolah_profil'; // Nama database yang Anda buat
$username = 'root'; // Username default Laragon/XAMPP
$password = ''; // Password default Laragon/XAMPP (kosongkan jika tidak ada)

try {
    // Buat objek PDO untuk koneksi
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set atribut PDO untuk penanganan error yang lebih baik
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Mengambil data sebagai array asosiatif secara default

    //echo "Koneksi database berhasil!"; // Anda bisa mengaktifkan ini untuk menguji koneksi

} catch (PDOException $e) {
    // Tangani error koneksi
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
