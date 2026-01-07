<?php
session_start();
require_once '../config/database.php';

check_role([1]);
$page_title = 'Persetujuan Pengajuan';

// Filter status
$status_filter = isset($_GET['status']) ? (int)$_GET['status'] : 1;

// Ambil semua pengajuan
$query = "SELECT p.*, a.nama_aula, s.nama_seksi, st.nama_status, st.warna,
          u.nama_lengkap as pemohon, us.nama_lengkap as disetujui_oleh_nama
          FROM pengajuan_aula p
          LEFT JOIN aula a ON p.aula_id = a.id
          LEFT JOIN seksi s ON p.seksi_id = s.id
          LEFT JOIN status_pengajuan st ON p.status_id = st.id
          LEFT JOIN users u ON p.user_id = u.id
          LEFT JOIN users us ON p.disetujui_oleh = us.id
          WHERE p.status_id = $status_filter
          ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);

// Hitung jumlah pengajuan menunggu
$count_menunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 1"))['total'];

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Persetujuan Pengajuan Aula</h3>
        <?php if ($count_menunggu > 0): ?>
        <span class="badge badge-warning badge-lg"><?php echo $count_menunggu; ?> Menunggu</span>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <!-- Filter Status -->
        <div class="filter-box">
            <strong>Filter Status:</strong>
            <a href="?status=1" class="btn btn-sm <?php echo $status_filter == 1 ? 'btn-warning' : 'btn-outline-warning'; ?>">
                Menunggu (<?php echo $count_menunggu; ?>)
            </a>
            <a href="?status=2" class="btn btn-sm <?php echo $status_filter == 2 ? 'btn-success' : 'btn-outline-success'; ?>">
                Disetujui
            </a>
            <a href="?status=3" class="btn btn-sm <?php echo $status_filter == 3 ? 'btn-danger' : 'btn-outline-danger'; ?>">
                Ditolak
            </a>
            <a href="?status=4" class="btn btn-sm <?php echo $status_filter == 4 ? 'btn-secondary' : 'btn-outline-secondary'; ?>">
                Dibatalkan
            </a>
        </div>

        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">
            Tidak ada pengajuan dengan status ini.
        </div>
        <?php else: ?>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pemohon</th>
                        <th>Seksi</th>
                        <th>Kegiatan</th>
                        <th>Aula</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo $row['kode_pengajuan']; ?></strong></td>
                        <td><?php echo $row['pemohon']; ?></td>
                        <td><?php echo $row['nama_seksi']; ?></td>
                        <td><?php echo $row['nama_kegiatan']; ?></td>
                        <td><?php echo $row['nama_aula']; ?></td>
                        <td>
                            <small>
                                <?php echo format_tanggal($row['tanggal_mulai']); ?><br>
                                s/d <?php echo format_tanggal($row['tanggal_selesai']); ?>
                            </small>
                        </td>
                        <td>
                            <small>
                                <?php echo substr($row['waktu_mulai'], 0, 5); ?><br>
                                <?php echo substr($row['waktu_selesai'], 0, 5); ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $row['warna']; ?>">
                                <?php echo $row['nama_status']; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                👁️ Detail
                            </button>
                            
                            <?php if ($row['status_id'] == 1): // Menunggu Persetujuan ?>
                            <button class="btn btn-sm btn-success" onclick="setujui(<?php echo $row['id']; ?>, '<?php echo $row['kode_pengajuan']; ?>')">
                                ✅ Setujui
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="tolak(<?php echo $row['id']; ?>, '<?php echo $row['kode_pengajuan']; ?>')">
                                ❌ Tolak
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Detail Pengajuan</h3>
            <span class="close" onclick="closeDetailModal()">&times;</span>
        </div>
        <div class="modal-body" id="detailContent"></div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div id="tolakModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tolak Pengajuan</h3>
            <span class="close" onclick="closeTolakModal()">&times;</span>
        </div>
        
        <form method="POST" action="../proses/persetujuan.php">
            <input type="hidden" name="action" value="tolak">
            <input type="hidden" name="id" id="tolakId">
            
            <div class="modal-body">
                <p>Kode Pengajuan: <strong id="tolakKode"></strong></p>
                
                <div class="form-group">
                    <label for="alasan_penolakan">Alasan Penolakan *</label>
                    <textarea id="alasan_penolakan" name="alasan_penolakan" class="form-control" 
                              rows="4" required></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeTolakModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Setujui -->
<form id="setujuiForm" method="POST" action="../proses/persetujuan.php" style="display: none;">
    <input type="hidden" name="action" value="setujui">
    <input type="hidden" name="id" id="setujuiId">
</form>

<script>
function viewDetail(data) {
    const statusBadge = `<span class="badge badge-${data.warna}">${data.nama_status}</span>`;
    
    let content = `
        <div class="detail-grid">
            <div class="detail-row"><strong>Kode Pengajuan:</strong><span>${data.kode_pengajuan}</span></div>
            <div class="detail-row"><strong>Status:</strong><span>${statusBadge}</span></div>
            <div class="detail-row"><strong>Nama Pemohon:</strong><span>${data.nama_pemohon}</span></div>
            <div class="detail-row"><strong>User:</strong><span>${data.pemohon}</span></div>
            <div class="detail-row"><strong>Seksi:</strong><span>${data.nama_seksi}</span></div>
            <div class="detail-row"><strong>Aula:</strong><span>${data.nama_aula}</span></div>
            <div class="detail-row"><strong>Nama Kegiatan:</strong><span>${data.nama_kegiatan}</span></div>
            <div class="detail-row"><strong>Tanggal:</strong><span>${data.tanggal_mulai} s/d ${data.tanggal_selesai}</span></div>
            <div class="detail-row"><strong>Waktu:</strong><span>${data.waktu_mulai.substring(0, 5)} - ${data.waktu_selesai.substring(0, 5)}</span></div>
            <div class="detail-row"><strong>Jumlah Peserta:</strong><span>${data.jumlah_peserta || '-'}</span></div>
            <div class="detail-row"><strong>Keterangan:</strong><span>${data.keterangan || '-'}</span></div>
    `;
    
    if (data.disetujui_oleh_nama) {
        content += `<div class="detail-row"><strong>Diproses oleh:</strong><span>${data.disetujui_oleh_nama}</span></div>`;
        content += `<div class="detail-row"><strong>Tanggal Diproses:</strong><span>${data.tanggal_persetujuan}</span></div>`;
    }
    
    if (data.alasan_penolakan) {
        content += `<div class="detail-row"><strong>Alasan Penolakan:</strong><span style="color: red;">${data.alasan_penolakan}</span></div>`;
    }
    
    content += `</div>`;
    
    document.getElementById('detailContent').innerHTML = content;
    document.getElementById('detailModal').style.display = 'block';
}

function closeDetailModal() {
    document.getElementById('detailModal').style.display = 'none';
}

function setujui(id, kode) {
    if (confirm(`Yakin ingin menyetujui pengajuan ${kode}?`)) {
        document.getElementById('setujuiId').value = id;
        document.getElementById('setujuiForm').submit();
    }
}

function tolak(id, kode) {
    document.getElementById('tolakId').value = id;
    document.getElementById('tolakKode').textContent = kode;
    document.getElementById('tolakModal').style.display = 'block';
}

function closeTolakModal() {
    document.getElementById('tolakModal').style.display = 'none';
}
</script>

<?php include '../includes/footer.php'; ?>