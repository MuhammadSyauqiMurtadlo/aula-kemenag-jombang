<?php
session_start();
require_once '../config/database.php';

check_role([1]);
$page_title = 'Data Seksi';

$query = "SELECT * FROM seksi ORDER BY id ASC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Data Seksi / Bagian</h3>
        <button class="btn btn-primary" onclick="showModal('add')">➕ Tambah Seksi</button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Seksi</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['nama_seksi']; ?></strong></td>
                        <td><?php echo $row['keterangan']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editSeksi(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                ✏️ Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="hapusSeksi(<?php echo $row['id']; ?>, '<?php echo $row['nama_seksi']; ?>')">
                                🗑️ Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Seksi -->
<div id="seksiModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Seksi</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form id="seksiForm" action="../proses/seksi.php" method="POST">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="seksiId">
            
            <div class="form-group">
                <label for="nama_seksi">Nama Seksi *</label>
                <input type="text" id="nama_seksi" name="nama_seksi" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="../proses/seksi.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function showModal(action) {
    document.getElementById('seksiModal').style.display = 'block';
    document.getElementById('formAction').value = action;
    
    if (action === 'add') {
        document.getElementById('modalTitle').textContent = 'Tambah Seksi';
        document.getElementById('seksiForm').reset();
        document.getElementById('seksiId').value = '';
    }
}

function closeModal() {
    document.getElementById('seksiModal').style.display = 'none';
}

function editSeksi(data) {
    showModal('edit');
    document.getElementById('modalTitle').textContent = 'Edit Seksi';
    document.getElementById('seksiId').value = data.id;
    document.getElementById('nama_seksi').value = data.nama_seksi;
    document.getElementById('keterangan').value = data.keterangan;
}

function hapusSeksi(id, nama) {
    if (confirm('Yakin ingin menghapus seksi "' + nama + '"?')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('seksiModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../includes/footer.php'; ?>