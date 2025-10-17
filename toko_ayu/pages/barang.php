<?php
// Get all barang
$barang = queryAll("SELECT * FROM barang ORDER BY id_barang DESC");

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $barang = queryAll("SELECT * FROM barang WHERE nama_barang LIKE '%$search%' ORDER BY id_barang DESC");
}
?>

<div class="page-header">
    <div>
        <h2 class="page-title neon-text">Data Barang</h2>
        <p class="page-subtitle">Kelola data barang yang dijual</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambahBarang')">
        <i class="fas fa-plus"></i> Tambah Barang
    </button>
</div>

<div class="glass-card neon-border">
    <!-- Search Box -->
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
            type="text" 
            class="form-control" 
            placeholder="Cari barang..."
            id="searchInput"
            onkeyup="searchTable('searchInput', 'tableBarang')"
        >
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="table" id="tableBarang">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barang as $item): ?>
                <tr>
                    <td><?= $item['id_barang'] ?></td>
                    <td><?= $item['nama_barang'] ?></td>
                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td><?= $item['stok'] ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-secondary btn-sm" onclick='editBarang(<?= json_encode($item) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="api/barang_crud.php?action=delete&id=<?= $item['id_barang'] ?>" 
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

<!-- Modal Tambah Barang -->
<div class="modal" id="modalTambahBarang">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title neon-text">Tambah Barang</h3>
            <button class="modal-close" onclick="closeModal('modalTambahBarang')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="api/barang_crud.php?action=add" method="POST">
            <div class="form-group">
                <label class="form-label">Nama Barang</label>
                <input 
                    type="text" 
                    name="nama_barang" 
                    id="nama_barang"
                    class="form-control" 
                    placeholder="Masukkan nama barang"
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label">Harga</label>
                <input 
                    type="number" 
                    name="harga" 
                    class="form-control" 
                    placeholder="Masukkan harga"
                    min="0"
                    step="0.01"
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label">Stok</label>
                <input 
                    type="number" 
                    name="stok" 
                    class="form-control" 
                    placeholder="Masukkan stok"
                    min="0"
                    required
                >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahBarang')">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Barang -->
<div class="modal" id="modalEditBarang">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title neon-text">Edit Barang</h3>
            <button class="modal-close" onclick="closeModal('modalEditBarang')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="api/barang_crud.php?action=edit" method="POST">
            <input type="hidden" name="id_barang" id="edit_id_barang">
            <div class="form-group">
                <label class="form-label">Nama Barang</label>
                <input 
                    type="text" 
                    name="nama_barang" 
                    id="edit_nama_barang"
                    class="form-control" 
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label">Harga</label>
                <input 
                    type="number" 
                    name="harga" 
                    id="edit_harga"
                    class="form-control" 
                    min="0"
                    step="0.01"
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label">Stok</label>
                <input 
                    type="number" 
                    name="stok" 
                    id="edit_stok"
                    class="form-control" 
                    min="0"
                    required
                >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditBarang')">
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
function editBarang(data) {
    document.getElementById('edit_id_barang').value = data.id_barang;
    document.getElementById('edit_nama_barang').value = data.nama_barang;
    document.getElementById('edit_harga').value = data.harga;
    document.getElementById('edit_stok').value = data.stok;
    openModal('modalEditBarang');
}
</script>