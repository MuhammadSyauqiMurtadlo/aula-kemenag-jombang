<?php
session_start();
require_once '../config/database.php';

check_role([3]);
$page_title = 'Status Pengajuan';

// Ambil pengajuan milik user yang login
$query = "SELECT p.*, a.nama_aula, s.nama_seksi, st.nama_status, st.warna,
          u.nama_lengkap as disetujui_oleh_nama
          FROM pengajuan_aula p
          LEFT JOIN aula a ON p.aula_id = a.id
          LEFT JOIN seksi s ON p.seksi_id = s.id
          LEFT JOIN status_pengajuan st ON p.status_id = st.id
          LEFT JOIN users u ON p.disetujui_oleh = u.id
          WHERE p.user_id = {$_SESSION['user_id']}
          ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Status Pengajuan Peminjaman Aula</h3>
        <a href="pengajuan.php" class="btn btn-primary">➕ Ajukan Baru</a>
    </div>

    <div class="card-body">
        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">
            Belum ada pengajuan. <a href="pengajuan.php">Ajukan sekarang</a>
        </div>
        <?php else: ?>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
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
                        <td><?php echo $row['nama_kegiatan']; ?></td>
                        <td><?php echo $row['nama_aula']; ?></td>
                        <td>
                            <?php echo format_tanggal($row['tanggal_mulai']); ?><br>
                            s/d <?php echo format_tanggal($row['tanggal_selesai']); ?>
                        </td>
                        <td>
                            <?php echo substr($row['waktu_mulai'], 0, 5); ?> - 
                            <?php echo substr($row['waktu_selesai'], 0, 5); ?>
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
                            <form method="POST" action="../proses/pengajuan.php" style="display: inline;">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                    ❌ Batalkan
                                </button>
                            </form>
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
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <div class="modal-body" id="detailContent">
            <!-- Content akan diisi oleh JavaScript -->
        </div>
        
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
function viewDetail(data) {
    const statusBadge = `<span class="badge badge-${data.warna}">${data.nama_status}</span>`;
    
    let content = `
        <div class="detail-grid">
            <div class="detail-row">
                <strong>Kode Pengajuan:</strong>
                <span>${data.kode_pengajuan}</span>
            </div>
            <div class="detail-row">
                <strong>Status:</strong>
                <span>${statusBadge}</span>
            </div>
            <div class="detail-row">
                <strong>Nama Pemohon:</strong>
                <span>${data.nama_pemohon}</span>
            </div>
            <div class="detail-row">
                <strong>Seksi:</strong>
                <span>${data.nama_seksi}</span>
            </div>
            <div class="detail-row">
                <strong>Aula:</strong>
                <span>${data.nama_aula}</span>
            </div>
            <div class="detail-row">
                <strong>Nama Kegiatan:</strong>
                <span>${data.nama_kegiatan}</span>
            </div>
            <div class="detail-row">
                <strong>Tanggal:</strong>
                <span>${data.tanggal_mulai} s/d ${data.tanggal_selesai}</span>
            </div>
            <div class="detail-row">
                <strong>Waktu:</strong>
                <span>${data.waktu_mulai.substring(0, 5)} - ${data.waktu_selesai.substring(0, 5)}</span>
            </div>
            <div class="detail-row">
                <strong>Jumlah Peserta:</strong>
                <span>${data.jumlah_peserta || '-'}</span>
            </div>
            <div class="detail-row">
                <strong>Keterangan:</strong>
                <span>${data.keterangan || '-'}</span>
            </div>
    `;
    
    if (data.disetujui_oleh_nama) {
        content += `
            <div class="detail-row">
                <strong>Diproses oleh:</strong>
                <span>${data.disetujui_oleh_nama}</span>
            </div>
            <div class="detail-row">
                <strong>Tanggal Diproses:</strong>
                <span>${data.tanggal_persetujuan}</span>
            </div>
        `;
    }
    
    if (data.alasan_penolakan) {
        content += `
            <div class="detail-row">
                <strong>Alasan Penolakan:</strong>
                <span style="color: red;">${data.alasan_penolakan}</span>
            </div>
        `;
    }
    
    content += `</div>`;
    
    document.getElementById('detailContent').innerHTML = content;
    document.getElementById('detailModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include '../includes/footer.php'; ?>