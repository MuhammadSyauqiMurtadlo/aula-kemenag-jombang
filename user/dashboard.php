<?php
session_start();
require_once '../config/database.php';

check_role([3]);
$page_title = 'Dashboard User';

$user_id = $_SESSION['user_id'];
$seksi_id = $_SESSION['seksi_id'];

// Statistik pengajuan user
$total_pengajuan = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE user_id = $user_id"))['total'];

$menunggu = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE user_id = $user_id AND status_id = 1"))['total'];

$disetujui = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE user_id = $user_id AND status_id = 2"))['total'];

$ditolak = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE user_id = $user_id AND status_id = 3"))['total'];

// Pengajuan terbaru user
$pengajuan_terbaru = mysqli_query($conn,
    "SELECT p.*, a.nama_aula, st.nama_status, st.warna
     FROM pengajuan_aula p
     LEFT JOIN aula a ON p.aula_id = a.id
     LEFT JOIN status_pengajuan st ON p.status_id = st.id
     WHERE p.user_id = $user_id
     ORDER BY p.created_at DESC
     LIMIT 5"
);

// Jadwal aula tersedia minggu ini
$today = date('Y-m-d');
$next_week = date('Y-m-d', strtotime('+7 days'));

$jadwal_terpakai = mysqli_query($conn,
    "SELECT p.*, a.nama_aula
     FROM pengajuan_aula p
     LEFT JOIN aula a ON p.aula_id = a.id
     WHERE p.status_id IN (1, 2)
     AND p.tanggal_mulai BETWEEN '$today' AND '$next_week'
     ORDER BY p.tanggal_mulai, p.waktu_mulai"
);

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card stat-primary">
        <div class="stat-icon">📋</div>
        <div class="stat-info">
            <h3><?php echo $total_pengajuan; ?></h3>
            <p>Total Pengajuan</p>
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
                <h3>Pengajuan Terbaru Saya</h3>
                <a href="pengajuan.php" class="btn btn-sm btn-primary">➕ Ajukan Baru</a>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($pengajuan_terbaru) == 0): ?>
                <p class="text-muted">Belum ada pengajuan. <a href="pengajuan.php">Ajukan sekarang</a></p>
                <?php else: ?>
                <div class="list-group">
                    <?php while ($p = mysqli_fetch_assoc($pengajuan_terbaru)): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo $p['nama_kegiatan']; ?></strong>
                            <span class="badge badge-<?php echo $p['warna']; ?>">
                                <?php echo $p['nama_status']; ?>
                            </span>
                        </div>
                        <small>
                            <?php echo $p['nama_aula']; ?> | <?php echo format_tanggal($p['tanggal_mulai']); ?>
                        </small>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="mt-3">
                    <a href="status.php" class="btn btn-sm btn-outline-primary btn-block">Lihat Semua Status</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Jadwal Aula Minggu Ini</h3>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($jadwal_terpakai) == 0): ?>
                <p class="text-muted">Tidak ada jadwal minggu ini</p>
                <?php else: ?>
                <div class="list-group">
                    <?php while ($j = mysqli_fetch_assoc($jadwal_terpakai)): ?>
                    <div class="list-group-item">
                        <strong><?php echo $j['nama_kegiatan']; ?></strong><br>
                        <small>
                            <?php echo $j['nama_aula']; ?><br>
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

<div class="card">
    <div class="card-body text-center">
        <h4>Perlu Mengajukan Peminjaman Aula?</h4>
        <p class="text-muted">Ajukan peminjaman aula untuk kegiatan seksi Anda</p>
        <a href="pengajuan.php" class="btn btn-primary btn-lg">➕ Ajukan Peminjaman Aula</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>