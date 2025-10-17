<?php
require_once '../config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ADD PEMBELI
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pembeli = trim($_POST['nama_pembeli']);
    $alamat = trim($_POST['alamat']);
    
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO pembeli (nama_pembeli, alamat) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_pembeli, $alamat);
    
    if ($stmt->execute()) {
        header("Location: ../index.php?page=pembeli&success=Data pembeli berhasil ditambahkan");
    } else {
        header("Location: ../index.php?page=pembeli&error=Gagal menambahkan data");
    }
    
    $stmt->close();
    $conn->close();
}

// EDIT PEMBELI
elseif ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pembeli = intval($_POST['id_pembeli']);
    $nama_pembeli = trim($_POST['nama_pembeli']);
    $alamat = trim($_POST['alamat']);
    
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE pembeli SET nama_pembeli = ?, alamat = ? WHERE id_pembeli = ?");
    $stmt->bind_param("ssi", $nama_pembeli, $alamat, $id_pembeli);
    
    if ($stmt->execute()) {
        header("Location: ../index.php?page=pembeli&success=Data pembeli berhasil diupdate");
    } else {
        header("Location: ../index.php?page=pembeli&error=Gagal mengupdate data");
    }
    
    $stmt->close();
    $conn->close();
}

// DELETE PEMBELI
elseif ($action == 'delete') {
    $id_pembeli = intval($_GET['id']);
    
    // Check if pembeli is used in transaksi
    $check = queryOne("SELECT COUNT(*) as total FROM transaksi WHERE id_pembeli = $id_pembeli");
    
    if ($check['total'] > 0) {
        header("Location: ../index.php?page=pembeli&error=Pembeli tidak bisa dihapus karena sudah ada transaksi");
        exit();
    }
    
    $result = execute("DELETE FROM pembeli WHERE id_pembeli = $id_pembeli");
    
    if ($result) {
        header("Location: ../index.php?page=pembeli&success=Data pembeli berhasil dihapus");
    } else {
        header("Location: ../index.php?page=pembeli&error=Gagal menghapus data");
    }
}

else {
    header("Location: ../index.php?page=pembeli");
}
?>
