<?php
// Konfigurasi Database
define('DB_HOST', '127.0.0.1:3307');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'toko_tas');

// Koneksi Database
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Function helper untuk query
function query($sql) {
    $conn = getConnection();
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

// Function untuk mendapatkan satu row
function queryOne($sql) {
    $result = query($sql);
    return $result ? $result->fetch_assoc() : null;
}

// Function untuk mendapatkan semua rows
function queryAll($sql) {
    $result = query($sql);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

// Function untuk execute (INSERT, UPDATE, DELETE)
function execute($sql) {
    $conn = getConnection();
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

// Function untuk insert data dengan prepared statement
function insertData($table, $data) {
    $conn = getConnection();
    
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
    
    $conn->close();
    return false;
}

// Function untuk update data
function updateData($table, $data, $where) {
    $conn = getConnection();
    
    $setClause = [];
    foreach ($data as $key => $value) {
        $setClause[] = "$key = ?";
    }
    $setClause = implode(', ', $setClause);
    
    $sql = "UPDATE $table SET $setClause WHERE $where";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
    
    $conn->close();
    return false;
}

// Function untuk delete data
function deleteData($table, $where) {
    $sql = "DELETE FROM $table WHERE $where";
    return execute($sql);
}
?>