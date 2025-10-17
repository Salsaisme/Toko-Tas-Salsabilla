<?php
require_once 'config.php';

// Routing sederhana
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Validasi page
$allowedPages = ['dashboard', 'barang', 'pembeli', 'transaksi', 'laporan'];
if (!in_array($page, $allowedPages)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Tas Salsabilla - UTS Pemrograman Basis Data</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Background Decorative -->
    <div class="bg-decorative">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar glass-strong neon-border">
        <div class="sidebar-header">
            <div class="logo-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="logo-text">
                <h1 class="neon-text">Toko Tas</h1>
                <p class="subtitle">Salsabilla</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="?page=dashboard" class="nav-item <?= $page == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="?page=barang" class="nav-item <?= $page == 'barang' ? 'active' : '' ?>">
                <i class="fas fa-box"></i>
                <span>Barang</span>
            </a>
            <a href="?page=pembeli" class="nav-item <?= $page == 'pembeli' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Pembeli</span>
            </a>
            <a href="?page=transaksi" class="nav-item <?= $page == 'transaksi' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Transaksi</span>
            </a>
            <a href="?page=laporan" class="nav-item <?= $page == 'laporan' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i>
                <span>Laporan Penjualan</span>
            </a>
        </nav>

        <div class="sidebar-footer glass">
            <p class="footer-text">UTS Pemrograman Basis Data</p>
            <p class="footer-version">Toko Tas Salsabilla v1.0</p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?php
            $filePath = "pages/{$page}.php";
            if (file_exists($filePath)) {
                include $filePath;
            } else {
                echo "<div class='glass-card'><h2>Halaman tidak ditemukan</h2></div>";
            }
            ?>
        </div>
    </main>

    <script src="assets/js/script.js"></script>
</body>
</html>