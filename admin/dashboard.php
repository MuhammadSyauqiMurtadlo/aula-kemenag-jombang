<?php
session_start();
require_once '../config/database.php';

check_role([1]);
$page_title = 'Dashboard Admin';

// Statistik
$total_aula = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM aula WHERE status = 'aktif'"))['total'];
$total_seksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM seksi"))['total'];
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE status = 'aktif'"))['total'];
$menunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 1"))['total'];
$disetujui = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 2"))['total'];
$ditolak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 3"))['total'];

// Pengajuan terbaru menunggu
$pengajuan_baru = mysqli_query($conn, 
    "SELECT p.*, a.nama_aula, s.nama_seksi, u.nama_lengkap as pemohon
     FROM pengajuan_aula p
     LEFT JOIN aula a ON p.aula_id = a.id
     LEFT JOIN seksi s ON p.seksi_id = s.id
     LEFT JOIN users u ON p.user_id = u.id
     WHERE p.status_id = 1
     ORDER BY p.created_at DESC
     LIMIT 5"
);

// Jadwal aula minggu ini
$today = date('Y-m-d');
$next_week = date('Y-m-d', strtotime('+7 days'));

$jadwal_minggu_ini = mysqli_query($conn,
    "SELECT p.*, a.nama_aula, s.nama_seksi
     FROM pengajuan_aula p
     LEFT JOIN aula a ON p.aula_id = a.id
     LEFT JOIN seksi s ON p.seksi_id = s.id
     WHERE p.status_id = 2
     AND p.tanggal_mulai BETWEEN '$today' AND '$next_week'
     ORDER BY p.tanggal_mulai, p.waktu_mulai"
);

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card stat-primary">
        <div class="stat-icon">🏢</div>
        <div class="stat-info">
            <h3><?php echo $total_aula; ?></h3>
            <p>Aula Aktif</p>
        </div>
    </div>
    
    <div class="stat-card stat-info">
        <div class="stat-icon">📁</div>
        <div class="stat-info">
            <h3><?php echo $total_seksi; ?></h3>
            <p>Seksi</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <h3><?php echo $total_user; ?></h3>
            <p>User Aktif</p>
        </div>
    </div>
    
    <div class="stat-card stat-warning">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <h3><?php echo $menunggu; ?></h3>
            <p>Menunggu</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <h3><?php echo $disetujui; ?></h3>
            <p>Disetujui</p>
        </div>
    </div>
    
    <div class="stat-card stat-danger">
        <div class="stat-icon">❌</div>
        <div class="stat-info">
            <h3><?php echo $ditolak; ?></h3>
            <p>Ditolak</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Pengajuan Menunggu Persetujuan</h3>
                <a href="persetujuan.php" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($pengajuan_baru) == 0): ?>
                <p class="text-muted">Tidak ada pengajuan menunggu</p>
                <?php else: ?>
                <div class="list-group">
                    <?php while ($p = mysqli_fetch_assoc($pengajuan_baru)): ?>
                    <div class="list-group-item">
                        <strong><?php echo $p['kode_pengajuan']; ?></strong><br>
                        <small>
                            <?php echo $p['pemohon']; ?> - <?php echo $p['nama_seksi']; ?><br>
                            <?php echo $p['nama_aula']; ?> | <?php echo format_tanggal($p['tanggal_mulai']); ?>
                        </small>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Jadwal Minggu Ini</h3>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($jadwal_minggu_ini) == 0): ?>
                <p class="text-muted">Tidak ada jadwal minggu ini</p>
                <?php else: ?>
                <div class="list-group">
                    <?php while ($j = mysqli_fetch_assoc($jadwal_minggu_ini)): ?>
                    <div class="list-group-item">
                        <strong><?php echo $j['nama_kegiatan']; ?></strong><br>
                        <small>
                            <?php echo $j['nama_aula']; ?> - <?php echo $j['nama_seksi']; ?><br>
                            <?php echo format_tanggal($j['tanggal_mulai']); ?> | 
                            <?php echo substr($j['waktu_mulai'], 0, 5); ?> - <?php echo substr($j['waktu_selesai'], 0, 5); ?>
                        </small>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>