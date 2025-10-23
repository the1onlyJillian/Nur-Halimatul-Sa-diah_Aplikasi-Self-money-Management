<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'tambah_target') {
        $nama_target = $_POST['nama_target'];
        $target_jumlah = $_POST['target_jumlah'];
        $tanggal_target = $_POST['tanggal_target'];
        $kategori = $_POST['kategori'];
        
        $stmt = $conn->prepare("INSERT INTO target_keuangan (nama_target, target_jumlah, tanggal_target, kategori) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $nama_target, $target_jumlah, $tanggal_target, $kategori);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Target berhasil ditambahkan!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambah target!']);
        }
        $stmt->close();
        
    } elseif ($action == 'update_target') {
        $id = $_POST['id'];
        $terkumpul = $_POST['terkumpul'];
        
        $stmt = $conn->prepare("UPDATE target_keuangan SET terkumpul = ? WHERE id = ?");
        $stmt->bind_param("di", $terkumpul, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Target berhasil diupdate!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update target!']);
        }
        $stmt->close();
        
    } elseif ($action == 'hapus_target') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM target_keuangan WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Target berhasil dihapus!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus target!']);
        }
        $stmt->close();
    }
    
    exit;
}

// Untuk GET request - ambil data target
$targets = $conn->query("SELECT * FROM target_keuangan ORDER BY created_at DESC");
$target_data = [];
while ($row = $targets->fetch_assoc()) {
    $persentase = ($row['terkumpul'] / $row['target_jumlah']) * 100;
    $row['persentase'] = round($persentase, 1);
    $row['sisa_hari'] = max(0, floor((strtotime($row['tanggal_target']) - time()) / (60 * 60 * 24)));
    $target_data[] = $row;
}

echo json_encode($target_data);
?>