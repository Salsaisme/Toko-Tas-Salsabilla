<?php
// Get all data
$transaksi = queryAll("
    SELECT t.*, b.nama_barang, b.harga, p.nama_pembeli 
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    JOIN pembeli p ON t.id_pembeli = p.id_pembeli
    ORDER BY t.id_transaksi DESC
");

$barang = queryAll("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang");
$pembeli = queryAll("SELECT * FROM pembeli ORDER BY nama_pembeli");
?>

<div class="page-header">
    <div>
        <h2 class="page-title neon-text">Data Transaksi</h2>
        <p class="page-subtitle">Kelola data transaksi penjualan</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambahTransaksi')">
        <i class="fas fa-plus"></i> Tambah Transaksi
    </button>
</div>

<div class="glass-card neon-border">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pembeli</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transaksi as $item): ?>
                <tr>
                    <td><?= $item['id_transaksi'] ?></td>
                    <td><?= $item['nama_pembeli'] ?></td>
                    <td><?= $item['nama_barang'] ?></td>
                    <td><?= $item['jumlah'] ?></td>
                    <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-secondary btn-sm" onclick='editTransaksi(<?= json_encode($item) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="api/transaksi_crud.php?action=delete&id=<?= $item['id_transaksi'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirmDelete()">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal" id="modalTambahTransaksi">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title neon-text">Tambah Transaksi</h3>
            <button class="modal-close" onclick="closeModal('modalTambahTransaksi')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="api/transaksi_crud.php?action=add" method="POST" onsubmit="return validateStock()">
            <div class="form-group">
                <label class="form-label">Pembeli</label>
                <select name="id_pembeli" class="form-control" required>
                    <option value="">Pilih pembeli</option>
                    <?php foreach ($pembeli as $p): ?>
                    <option value="<?= $p['id_pembeli'] ?>"><?= $p['nama_pembeli'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Barang</label>
                <select name="id_barang" id="id_barang" class="form-control" onchange="calculateTotal()" required>
                    <option value="">Pilih barang</option>
                    <?php foreach ($barang as $b): ?>
                    <option 
                        value="<?= $b['id_barang'] ?>" 
                        data-harga="<?= $b['harga'] ?>"
                        data-stok="<?= $b['stok'] ?>"
                    >
                        <?= $b['nama_barang'] ?> (Stok: <?= $b['stok'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="alert alert-info" id="stokInfo" style="display: none;">
                <i class="fas fa-info-circle"></i>
                <span id="stokText"></span>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah</label>
                <input 
                    type="number" 
                    name="jumlah" 
                    id="jumlah"
                    class="form-control" 
                    placeholder="Masukkan jumlah"
                    min="1"
                    onchange="calculateTotal()"
                    oninput="calculateTotal()"
                    required
                >
            </div>
            <div class="glass" style="padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #e5e7eb;">Total Harga:</span>
                    <span id="total_display" style="font-size: 1.5rem; color: #67e8f9; text-shadow: 0 0 10px rgba(103, 232, 249, 0.8);">
                        Rp 0
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahTransaksi')">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Transaksi -->
<div class="modal" id="modalEditTransaksi">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title neon-text">Edit Transaksi</h3>
            <button class="modal-close" onclick="closeModal('modalEditTransaksi')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="api/transaksi_crud.php?action=edit" method="POST">
            <input type="hidden" name="id_transaksi" id="edit_id_transaksi">
            <div class="form-group">
                <label class="form-label">Pembeli</label>
                <select name="id_pembeli" id="edit_id_pembeli" class="form-control" required>
                    <option value="">Pilih pembeli</option>
                    <?php foreach ($pembeli as $p): ?>
                    <option value="<?= $p['id_pembeli'] ?>"><?= $p['nama_pembeli'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Barang</label>
                <select name="id_barang" id="edit_id_barang" class="form-control" required>
                    <option value="">Pilih barang</option>
                    <?php foreach ($barang as $b): ?>
                    <option value="<?= $b['id_barang'] ?>"><?= $b['nama_barang'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah</label>
                <input 
                    type="number" 
                    name="jumlah" 
                    id="edit_jumlah"
                    class="form-control" 
                    min="1"
                    required
                >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditTransaksi')">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editTransaksi(data) {
    document.getElementById('edit_id_transaksi').value = data.id_transaksi;
    document.getElementById('edit_id_pembeli').value = data.id_pembeli;
    document.getElementById('edit_id_barang').value = data.id_barang;
    document.getElementById('edit_jumlah').value = data.jumlah;
    openModal('modalEditTransaksi');
}

// Update calculateTotal untuk menampilkan info stok
document.getElementById('id_barang')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const stok = selectedOption.getAttribute('data-stok');
    
    if (stok) {
        document.getElementById('stokInfo').style.display = 'flex';
        document.getElementById('stokText').textContent = `Stok tersedia: ${stok}`;
    }
});
</script>