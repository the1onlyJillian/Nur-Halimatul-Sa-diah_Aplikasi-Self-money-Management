<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    
    // Validasi input
    if (empty($jenis) || empty($jumlah) || empty($kategori)) {
        echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
        exit;
    }
    
    if ($jumlah <= 0) {
        echo "<script>alert('Jumlah harus lebih dari 0!'); window.history.back();</script>";
        exit;
    }
    
    // Insert ke database
    $stmt = $conn->prepare("INSERT INTO transaksi (jenis, jumlah, kategori, deskripsi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $jenis, $jumlah, $kategori, $deskripsi);
    
    if ($stmt->execute()) {
        echo "<script>alert('Transaksi berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
    }
    
    $stmt->close();
} else {
    header('Location: index.php');
    exit;
}
?>