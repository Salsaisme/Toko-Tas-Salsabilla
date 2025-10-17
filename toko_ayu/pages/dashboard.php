<?php
// Get dashboard statistics
$totalTransaksi = queryOne("SELECT COUNT(*) as total FROM transaksi")['total'];
$totalPendapatan = queryOne("SELECT SUM(total_harga) as total FROM transaksi")['total'] ?? 0;

// Get barang terlaris
$barangTerlaris = queryOne("
    SELECT b.nama_barang, SUM(t.jumlah) as total_terjual 
    FROM transaksi t 
    JOIN barang b ON t.id_barang = b.id_barang 
    GROUP BY t.id_barang 
    ORDER BY total_terjual DESC 
    LIMIT 1
");
?>

<div class="page-header">
    <div>
        <h2 class="page-title neon-text">Dashboard</h2>
        <p class="page-subtitle">Ringkasan Penjualan Toko Tas Salsabilla</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card neon-border">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Transaksi</p>
                <h3 class="stat-value"><?= number_format($totalTransaksi) ?></h3>
            </div>
            <div class="stat-icon cyan">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="stat-card neon-border">
        <div class="stat-header">
            <div>
                <p class="stat-label">Total Pendapatan</p>
                <h3 class="stat-value">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h3>
            </div>
            <div class="stat-icon green">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="stat-card neon-border">
        <div class="stat-header">
            <div>
                <p class="stat-label">Barang Terlaris</p>
                <h3 class="stat-value"><?= $barangTerlaris['nama_barang'] ?? '-' ?></h3>
                <?php if ($barangTerlaris): ?>
                <p class="stat-subtitle"><?= $barangTerlaris['total_terjual'] ?> terjual</p>
                <?php endif; ?>
            </div>
            <div class="stat-icon purple">
                <i class="fas fa-award"></i>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Card -->
<div class="glass-card neon-border">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
        <i class="fas fa-chart-line" style="font-size: 1.5rem; color: var(--neon-cyan);"></i>
        <h3 style="font-size: 1.5rem;">Selamat Datang!</h3>
    </div>
    <p style="line-height: 1.6; color: #e5e7eb;">
        Sistem manajemen penjualan ini membantu Anda mengelola data barang, pembeli, 
        transaksi, dan laporan penjualan dengan mudah. Gunakan menu di sebelah kiri 
        untuk mengakses fitur-fitur yang tersedia.
    </p>
</div>