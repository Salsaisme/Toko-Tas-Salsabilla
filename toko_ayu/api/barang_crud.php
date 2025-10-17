<?php
require_once '../config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ADD BARANG
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = strtoupper(trim($_POST['nama_barang'])); // Convert to uppercase
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    
    // Validasi
    if ($stok < 0) {
        header("Location: ../index.php?page=barang&error=Stok tidak boleh negatif");
        exit();
    }
    
    if ($harga < 0) {
        header("Location: ../index.php?page=barang&error=Harga tidak boleh negatif");
        exit();
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, harga, stok) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $nama_barang, $harga, $stok);
    
    if ($stmt->execute()) {
        header("Location: ../index.php?page=barang&success=Data barang berhasil ditambahkan");
    } else {
        header("Location: ../index.php?page=barang&error=Gagal menambahkan data");
    }
    
    $stmt->close();
    $conn->close();
}

// EDIT BARANG
elseif ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = intval($_POST['id_barang']);
    $nama_barang = strtoupper(trim($_POST['nama_barang'])); // Convert to uppercase
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    
    // Validasi
    if ($stok < 0) {
        header("Location: ../index.php?page=barang&error=Stok tidak boleh negatif");
        exit();
    }
    
    if ($harga < 0) {
        header("Location: ../index.php?page=barang&error=Harga tidak boleh negatif");
        exit();
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, harga = ?, stok = ? WHERE id_barang = ?");
    $stmt->bind_param("sdii", $nama_barang, $harga, $stok, $id_barang);
    
    if ($stmt->execute()) {
        header("Location: ../index.php?page=barang&success=Data barang berhasil diupdate");
    } else {
        header("Location: ../index.php?page=barang&error=Gagal mengupdate data");
    }
    
    $stmt->close();
    $conn->close();
}

// DELETE BARANG
elseif ($action == 'delete') {
    $id_barang = intval($_GET['id']);
    
    // Check if barang is used in transaksi
    $check = queryOne("SELECT COUNT(*) as total FROM transaksi WHERE id_barang = $id_barang");
    
    if ($check['total'] > 0) {
        header("Location: ../index.php?page=barang&error=Barang tidak bisa dihapus karena sudah ada transaksi");
        exit();
    }
    
    $result = execute("DELETE FROM barang WHERE id_barang = $id_barang");
    
    if ($result) {
        header("Location: ../index.php?page=barang&success=Data barang berhasil dihapus");
    } else {
        header("Location: ../index.php?page=barang&error=Gagal menghapus data");
    }
}

else {
    header("Location: ../index.php?page=barang");
}
?>