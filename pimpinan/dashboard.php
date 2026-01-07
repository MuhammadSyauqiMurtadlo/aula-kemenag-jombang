<?php
session_start();
require_once '../config/database.php';

check_role([2]);
$page_title = 'Dashboard Pimpinan';

// Statistik bulan ini
$bulan_ini = date('Y-m');
$pengajuan_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM pengajuan_aula 
     WHERE DATE_FORMAT(created_at, '%Y-%m') = '$bulan_ini'"))['total'];

$disetujui_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula 
     WHERE status_id = 2 
     AND DATE_FORMAT(tanggal_persetujuan, '%Y-%m') = '$bulan_ini'"))['total'];

// Aula paling sering digunakan
$aula_populer = mysqli_query($conn,
    "SELECT a.nama_aula, COUNT(h.id) as total
     FROM histori_penggunaan h
     LEFT JOIN aula a ON h.aula_id = a.id
     GROUP BY h.aula_id
     ORDER BY total DESC
     LIMIT 5"
);

// Seksi paling aktif
$seksi_aktif = mysqli_query($conn,
    "SELECT s.nama_seksi, COUNT(p.id) as total
     FROM pengajuan_aula p
     LEFT JOIN seksi s ON p.seksi_id = s.id
     WHERE p.status_id = 2
     GROUP BY p.seksi_id
     ORDER BY total DESC
     LIMIT 5"
);

// Jadwal mendatang
$today = date('Y-m-d');
$jadwal_mendatang = mysqli_query($conn,
    "SELECT p.*, a.nama_aula, s.nama_seksi
     FROM pengajuan_aula p
     LEFT JOIN aula a ON p.aula_id = a.id
     LEFT JOIN seksi s ON p.seksi_id = s.id
     WHERE p.status_id = 2
     AND p.tanggal_mulai >= '$today'
     ORDER BY p.tanggal_mulai, p.waktu_mulai
     LIMIT 10"
);

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card stat-primary">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3><?php echo $pengajuan_bulan_ini; ?></h3>
            <p>Pengajuan Bulan Ini</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <h3><?php echo $disetujui_bulan_ini; ?></h3>
            <p>Disetujui Bulan Ini</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Aula Paling Populer</h3>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($aula_populer) == 0): ?>
                <p class="text-muted">Belum ada data</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Aula</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($aula_populer, 0);
                        while ($a = mysqli_fetch_assoc($aula_populer)): 
                        ?>
                        <tr>
                            <td><?php echo $a['nama_aula']; ?></td>
                            <td class="text-right"><strong><?php echo $a['total']; ?>x</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Seksi Paling Aktif</h3>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($seksi_aktif) == 0): ?>
                <p class="text-muted">Belum ada data</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Seksi</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($seksi_aktif, 0);
                        while ($s = mysqli_fetch_assoc($seksi_aktif)): 
                        ?>
                        <tr>
                            <td><?php echo $s['nama_seksi']; ?></td>
                            <td class="text-right"><strong><?php echo $s['total']; ?>x</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Jadwal Mendatang</h3>
        <a href="laporan.php" class="btn btn-sm btn-primary">Lihat Laporan Lengkap</a>
    </div>
    <div class="card-body">
        <?php if (mysqli_num_rows($jadwal_mendatang) == 0): ?>
        <p class="text-muted">Tidak ada jadwal mendatang</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Kegiatan</th>
                        <th>Aula</th>
                        <th>Seksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($jadwal_mendatang, 0);
                    while ($j = mysqli_fetch_assoc($jadwal_mendatang)): 
                    ?>
                    <tr>
                        <td><?php echo format_tanggal($j['tanggal_mulai']); ?></td>
                        <td><?php echo substr($j['waktu_mulai'], 0, 5); ?> - <?php echo substr($j['waktu_selesai'], 0, 5); ?></td>
                        <td><?php echo $j['nama_kegiatan']; ?></td>
                        <td><?php echo $j['nama_aula']; ?></td>
                        <td><?php echo $j['nama_seksi']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>