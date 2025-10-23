<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'keuangan_web';

// Buat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");
?>