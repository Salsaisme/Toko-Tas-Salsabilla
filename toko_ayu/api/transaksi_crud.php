<?php
require_once '../config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ADD TRANSAKSI
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pembeli = intval($_POST['id_pembeli']);
    $id_barang = intval($_POST['id_barang']);
    $jumlah = intval($_POST['jumlah']);
    
    // Get barang info
    $barang = queryOne("SELECT * FROM barang WHERE id_barang = $id_barang");
    
    if (!$barang) {
        header("Location: ../index.php?page=transaksi&error=Barang tidak ditemukan");
        exit();
    }
    
    // Validasi stok
    if ($jumlah > $barang['stok']) {
        header("Location: ../index.php?page=transaksi&error=Stok tidak mencukupi! Stok tersedia: {$barang['stok']}");
        exit();
    }
    
    if ($jumlah <= 0) {
        header("Location: ../index.php?page=transaksi&error=Jumlah harus lebih dari 0");
        exit();
    }
    
    // Calculate total
    $total_harga = $barang['harga'] * $jumlah;
    $tanggal = date('Y-m-d');
    
    $conn = getConnection();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert transaksi
        $stmt = $conn->prepare("INSERT INTO transaksi (id_pembeli, id_barang, jumlah, total_harga, tanggal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiids", $id_pembeli, $id_barang, $jumlah, $total_harga, $tanggal);
        $stmt->execute();
        $stmt->close();
        
        // Update stok barang
        $new_stok = $barang['stok'] - $jumlah;
        $stmt2 = $conn->prepare("UPDATE barang SET stok = ? WHERE id_barang = ?");
        $stmt2->bind_param("ii", $new_stok, $id_barang);
        $stmt2->execute();
        $stmt2->close();
        
        // Commit transaction
        $conn->commit();
        
        header("Location: ../index.php?page=transaksi&success=Transaksi berhasil ditambahkan");
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        header("Location: ../index.php?page=transaksi&error=Gagal menambahkan transaksi: " . $e->getMessage());
    }
    
    $conn->close();
}

// EDIT TRANSAKSI
elseif ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_transaksi = intval($_POST['id_transaksi']);
    $id_pembeli = intval($_POST['id_pembeli']);
    $id_barang = intval($_POST['id_barang']);
    $jumlah = intval($_POST['jumlah']);
    
    // Get old transaksi
    $old_transaksi = queryOne("SELECT * FROM transaksi WHERE id_transaksi = $id_transaksi");
    
    if (!$old_transaksi) {
        header("Location: ../index.php?page=transaksi&error=Transaksi tidak ditemukan");
        exit();
    }
    
    // Get barang info
    $barang = queryOne("SELECT * FROM barang WHERE id_barang = $id_barang");
    
    if (!$barang) {
        header("Location: ../index.php?page=transaksi&error=Barang tidak ditemukan");
        exit();
    }
    
    // Calculate available stock (current stock + old transaction quantity)
    $available_stok = $barang['stok'] + $old_transaksi['jumlah'];
    
    // Validasi stok
    if ($jumlah > $available_stok) {
        header("Location: ../index.php?page=transaksi&error=Stok tidak mencukupi! Stok tersedia: {$available_stok}");
        exit();
    }
    
    if ($jumlah <= 0) {
        header("Location: ../index.php?page=transaksi&error=Jumlah harus lebih dari 0");
        exit();
    }
    
    // Calculate total
    $total_harga = $barang['harga'] * $jumlah;
    
    $conn = getConnection();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Restore old stock
        $stmt = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
        $old_jumlah = $old_transaksi['jumlah'];
        $old_id_barang = $old_transaksi['id_barang'];
        $stmt->bind_param("ii", $old_jumlah, $old_id_barang);
        $stmt->execute();
        $stmt->close();
        
        // Update transaksi
        $stmt2 = $conn->prepare("UPDATE transaksi SET id_pembeli = ?, id_barang = ?, jumlah = ?, total_harga = ? WHERE id_transaksi = ?");
        $stmt2->bind_param("iiidi", $id_pembeli, $id_barang, $jumlah, $total_harga, $id_transaksi);
        $stmt2->execute();
        $stmt2->close();
        
        // Deduct new stock
        $stmt3 = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
        $stmt3->bind_param("ii", $jumlah, $id_barang);
        $stmt3->execute();
        $stmt3->close();
        
        // Commit transaction
        $conn->commit();
        
        header("Location: ../index.php?page=transaksi&success=Transaksi berhasil diupdate");
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        header("Location: ../index.php?page=transaksi&error=Gagal mengupdate transaksi: " . $e->getMessage());
    }
    
    $conn->close();
}

// DELETE TRANSAKSI
elseif ($action == 'delete') {
    $id_transaksi = intval($_GET['id']);
    
    // Get transaksi info
    $transaksi = queryOne("SELECT * FROM transaksi WHERE id_transaksi = $id_transaksi");
    
    if (!$transaksi) {
        header("Location: ../index.php?page=transaksi&error=Transaksi tidak ditemukan");
        exit();
    }
    
    $conn = getConnection();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Restore stock
        $stmt = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
        $jumlah = $transaksi['jumlah'];
        $id_barang = $transaksi['id_barang'];
        $stmt->bind_param("ii", $jumlah, $id_barang);
        $stmt->execute();
        $stmt->close();
        
        // Delete transaksi
        $stmt2 = $conn->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
        $stmt2->bind_param("i", $id_transaksi);
        $stmt2->execute();
        $stmt2->close();
        
        // Commit transaction
        $conn->commit();
        
        header("Location: ../index.php?page=transaksi&success=Transaksi berhasil dihapus");
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        header("Location: ../index.php?page=transaksi&error=Gagal menghapus transaksi: " . $e->getMessage());
    }
    
    $conn->close();
}

else {
    header("Location: ../index.php?page=transaksi");
}
?>