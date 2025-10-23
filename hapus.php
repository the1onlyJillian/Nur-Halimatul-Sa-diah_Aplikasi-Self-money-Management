<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM transaksi WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Transaksi berhasil dihapus!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error menghapus transaksi!'); window.location.href='index.php';</script>";
    }
    
    $stmt->close();
} else {
    header('Location: index.php');
    exit;
}
?>