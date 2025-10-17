<?php
// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build query
$sql = "
    SELECT t.*, b.nama_barang, p.nama_pembeli 
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    JOIN pembeli p ON t.id_pembeli = p.id_pembeli
    WHERE 1=1
";

if ($search) {
    $sql .= " AND p.nama_pembeli LIKE '%$search%'";
}

if ($startDate && $endDate) {
    $sql .= " AND t.tanggal BETWEEN '$startDate' AND '$endDate'";
}

$sql .= " ORDER BY t.tanggal DESC, t.id_transaksi DESC";

$transaksi = queryAll($sql);

// Calculate total
$totalPendapatan = 0;
foreach ($transaksi as $item) {
    $totalPendapatan += $item['total_harga'];
}
?>

<div class="page-header no-print">
    <div>
        <h2 class="page-title neon-text">Laporan Penjualan</h2>
        <p class="page-subtitle">Laporan transaksi penjualan lengkap</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button class="btn btn-secondary" onclick="exportToCSV('tableLaporan', 'laporan-penjualan.csv')">
            <i class="fas fa-download"></i> Export CSV
        </button>
        <button class="btn btn-primary" onclick="printReport()">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="glass-card neon-border no-print">
    <form method="GET" action="">
        <input type="hidden" name="page" value="laporan">
        <div class="filter-section">
            <div class="form-group">
                <label class="form-label">Cari Pembeli</label>
                <div class="search-box" style="margin-bottom: 0;">
                    <i class="fas fa-search"></i>
                    <input 
                        type="text" 
                        name="search"
                        class="form-control" 
                        placeholder="Nama pembeli..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input 
                    type="date" 
                    name="start_date"
                    class="form-control"
                    value="<?= htmlspecialchars($startDate) ?>"
                >
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Akhir</label>
                <input 
                    type="date" 
                    name="end_date"
                    class="form-control"
                    value="<?= htmlspecialchars($endDate) ?>"
                >
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <?php if ($search || $startDate || $endDate): ?>
            <a href="?page=laporan" class="btn btn-secondary">
                Reset Filter
            </a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Report Content -->
<div class="glass-card neon-border">
    <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.2); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Toko Tas Salsabilla</h3>
                <p style="color: #d1d5db;">Laporan Transaksi Penjualan</p>
            </div>
            <div style="text-align: right;">
                <p style="color: #d1d5db; margin-bottom: 0.25rem;">
                    Total Transaksi: <?= count($transaksi) ?>
                </p>
                <p style="font-size: 1.5rem; color: #67e8f9; text-shadow: 0 0 10px rgba(103, 232, 249, 0.8);">
                    Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                </p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table" id="tableLaporan">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pembeli</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($transaksi) > 0): ?>
                    <?php foreach ($transaksi as $item): ?>
                    <tr>
                        <td><?= $item['id_transaksi'] ?></td>
                        <td><?= $item['nama_pembeli'] ?></td>
                        <td><?= $item['nama_barang'] ?></td>
                        <td><?= $item['jumlah'] ?></td>
                        <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                        <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #9ca3af;">
                            Tidak ada data transaksi
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (count($transaksi) > 0): ?>
    <div style="border-top: 1px solid rgba(255, 255, 255, 0.2); padding-top: 1.5rem; margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #e5e7eb;">Total Pendapatan:</span>
            <span style="font-size: 1.75rem; color: #67e8f9; text-shadow: 0 0 10px rgba(103, 232, 249, 0.8);">
                Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
            </span>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>