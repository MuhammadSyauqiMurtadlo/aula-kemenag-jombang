<?php
session_start();
require_once '../config/database.php';

check_role([1]);
$page_title = 'Data User';

$query = "SELECT u.*, r.nama_role, s.nama_seksi 
          FROM users u 
          LEFT JOIN roles r ON u.role_id = r.id 
          LEFT JOIN seksi s ON u.seksi_id = s.id 
          ORDER BY u.id ASC";
$result = mysqli_query($conn, $query);

// Ambil data untuk dropdown
$roles = mysqli_query($conn, "SELECT * FROM roles");
$seksi = mysqli_query($conn, "SELECT * FROM seksi ORDER BY nama_seksi");

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Data User</h3>
        <button class="btn btn-primary" onclick="showModal('add')">➕ Tambah User</button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Role</th>
                        <th>Seksi</th>
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
                        <td><strong><?php echo $row['username']; ?></strong></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $row['role_id'] == 1 ? 'danger' : ($row['role_id'] == 2 ? 'warning' : 'primary'); ?>">
                                <?php echo $row['nama_role']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['nama_seksi'] ?? '-'; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $row['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                ✏️ Edit
                            </button>
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-sm btn-danger" onclick="hapusUser(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>')">
                                🗑️ Hapus
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div id="userModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah User</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form id="userForm" action="../proses/user.php" method="POST">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="userId">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap *</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role_id">Role *</label>
                        <select id="role_id" name="role_id" class="form-control" required onchange="toggleSeksi()">
                            <option value="">-- Pilih Role --</option>
                            <?php mysqli_data_seek($roles, 0); while ($r = mysqli_fetch_assoc($roles)): ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo $r['nama_role']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group" id="seksiGroup">
                        <label for="seksi_id">Seksi (untuk User)</label>
                        <select id="seksi_id" name="seksi_id" class="form-control">
                            <option value="">-- Pilih Seksi --</option>
                            <?php mysqli_data_seek($seksi, 0); while ($s = mysqli_fetch_assoc($seksi)): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['nama_seksi']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Password <span id="passwordNote">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" id="password" name="password" class="form-control">
                        <small class="text-muted">Min. 6 karakter</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="../proses/user.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function showModal(action) {
    document.getElementById('userModal').style.display = 'block';
    document.getElementById('formAction').value = action;
    
    if (action === 'add') {
        document.getElementById('modalTitle').textContent = 'Tambah User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('passwordNote').style.display = 'none';
        document.getElementById('password').required = true;
    }
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}

function toggleSeksi() {
    const roleId = document.getElementById('role_id').value;
    const seksiGroup = document.getElementById('seksiGroup');
    
    // Seksi hanya untuk User (role_id = 3)
    if (roleId == 3) {
        document.getElementById('seksi_id').required = true;
    } else {
        document.getElementById('seksi_id').required = false;
        document.getElementById('seksi_id').value = '';
    }
}

function editUser(data) {
    showModal('edit');
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('userId').value = data.id;
    document.getElementById('username').value = data.username;
    document.getElementById('nama_lengkap').value = data.nama_lengkap;
    document.getElementById('email').value = data.email || '';
    document.getElementById('no_hp').value = data.no_hp || '';
    document.getElementById('role_id').value = data.role_id;
    document.getElementById('seksi_id').value = data.seksi_id || '';
    document.getElementById('status').value = data.status;
    document.getElementById('passwordNote').style.display = 'inline';
    document.getElementById('password').required = false;
    
    toggleSeksi();
}

function hapusUser(id, username) {
    if (confirm('Yakin ingin menghapus user "' + username + '"?')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../includes/footer.php'; ?>