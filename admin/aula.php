<?php
session_status();
require_once '../config/database.php';

// Cek role admin
check_role([1]);

$page_title = 'Data Aula';

// Ambil data aula
$query = "SELECT * FROM aula ORDER BY id ASC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Data Aula</h3>
        <button class="btn btn-primary" onclick="showModal('add')">➕ Tambah Aula</button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Aula</th>
                        <th>Kapasitas</th>
                        <th>Fasilitas</th>
                        <th>Keterangan</th>
                        <th>Status</th>
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
                        <td><strong><?php echo $row['nama_aula']; ?></strong></td>
                        <td><?php echo $row['kapasitas']; ?> orang</td>
                        <td><?php echo $row['fasilitas']; ?></td>
                        <td><?php echo $row['keterangan']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $row['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editAula(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                ✏️ Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="hapusAula(<?php echo $row['id']; ?>, '<?php echo $row['nama_aula']; ?>')">
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

<!-- Modal Tambah/Edit Aula -->
<div id="aulaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Aula</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form id="aulaForm" action="../proses/aula.php" method="POST">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="aulaId">
            
            <div class="form-group">
                <label for="nama_aula">Nama Aula *</label>
                <input type="text" id="nama_aula" name="nama_aula" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="kapasitas">Kapasitas (orang)</label>
                <input type="number" id="kapasitas" name="kapasitas" class="form-control" min="1">
            </div>

            <div class="form-group">
                <label for="fasilitas">Fasilitas</label>
                <textarea id="fasilitas" name="fasilitas" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non Aktif</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Form untuk hapus -->
<form id="deleteForm" action="../proses/aula.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function showModal(action) {
    document.getElementById('aulaModal').style.display = 'block';
    document.getElementById('formAction').value = action;
    
    if (action === 'add') {
        document.getElementById('modalTitle').textContent = 'Tambah Aula';
        document.getElementById('aulaForm').reset();
        document.getElementById('aulaId').value = '';
    }
}

function closeModal() {
    document.getElementById('aulaModal').style.display = 'none';
}

function editAula(data) {
    showModal('edit');
    document.getElementById('modalTitle').textContent = 'Edit Aula';
    document.getElementById('aulaId').value = data.id;
    document.getElementById('nama_aula').value = data.nama_aula;
    document.getElementById('kapasitas').value = data.kapasitas;
    document.getElementById('fasilitas').value = data.fasilitas;
    document.getElementById('keterangan').value = data.keterangan;
    document.getElementById('status').value = data.status;
}

function hapusAula(id, nama) {
    if (confirm('Yakin ingin menghapus aula "' + nama + '"?')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Close modal ketika klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('aulaModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../includes/footer.php'; ?>