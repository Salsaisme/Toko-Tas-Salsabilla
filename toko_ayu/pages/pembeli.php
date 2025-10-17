<?php
// Get all pembeli
$pembeli = queryAll("SELECT * FROM pembeli ORDER BY id_pembeli DESC");
?>

<div class="page-header">
    <div>
        <h2 class="page-title neon-text">Data Pembeli</h2>
        <p class="page-subtitle">Kelola data pembeli</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambahPembeli')">
        <i class="fas fa-plus"></i> Tambah Pembeli
    </button>
</div>

<div class="glass-card neon-border">
    <!-- Search Box -->
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
            type="text" 
            class="form-control" 
            placeholder="Cari pembeli..."
            id="searchInput"
            onkeyup="searchTable('searchInput', 'tablePembeli')"
        >
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="table" id="tablePembeli">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pembeli</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembeli as $item): ?>
                <tr>
                    <td><?= $item['id_pembeli'] ?></td>
                    <td><?= $item['nama_pembeli'] ?></td>
                    <td><?= $item['alamat'] ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-secondary btn-sm" onclick='editPembeli(<?= json_encode($item) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="api/pembeli_crud.php?action=delete&id=<?= $item['id_pembeli'] ?>" 
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

<!-- Modal Tambah Pembeli -->
<div class="modal" id="modalTambahPembeli">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title neon-text">Tambah Pembeli</h3>
            <button class="modal-close" onclick="closeModal('modalTambahPembeli')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="api/pembeli_crud.php?action=add" method="POST">
            <div class="form-group">
                <label class="form-label">Nama Pembeli</label>
                <input 
                    type="text" 
                    name="nama_pembeli" 
                    class="form-control" 
                    placeholder="Masukkan nama pembeli"
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea 
                    name="alamat" 
                    class="form-control" 
                    placeholder="Masukkan alamat pembeli"
                    rows="3"
                    required
                ></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahPembeli')">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pembeli -->
<div class="modal" id="modalEditPembeli">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title neon-text">Edit Pembeli</h3>
            <button class="modal-close" onclick="closeModal('modalEditPembeli')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="api/pembeli_crud.php?action=edit" method="POST">
            <input type="hidden" name="id_pembeli" id="edit_id_pembeli">
            <div class="form-group">
                <label class="form-label">Nama Pembeli</label>
                <input 
                    type="text" 
                    name="nama_pembeli" 
                    id="edit_nama_pembeli"
                    class="form-control" 
                    required
                >
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea 
                    name="alamat" 
                    id="edit_alamat"
                    class="form-control" 
                    rows="3"
                    required
                ></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditPembeli')">
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
function editPembeli(data) {
    document.getElementById('edit_id_pembeli').value = data.id_pembeli;
    document.getElementById('edit_nama_pembeli').value = data.nama_pembeli;
    document.getElementById('edit_alamat').value = data.alamat;
    openModal('modalEditPembeli');
}
</script>
