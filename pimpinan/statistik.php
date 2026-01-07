<?php
session_start();
require_once '../config/database.php';

check_role([2]);
$page_title = 'Statistik';

// Statistik per bulan (6 bulan terakhir)
$bulan_stats = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i months"));
    $bulan_label = date('M Y', strtotime("-$i months"));
    
    $total = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as total FROM pengajuan_aula 
         WHERE DATE_FORMAT(created_at, '%Y-%m') = '$bulan'"))['total'];
    
    $disetujui = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as total FROM pengajuan_aula 
         WHERE status_id = 2 
         AND DATE_FORMAT(tanggal_persetujuan, '%Y-%m') = '$bulan'"))['total'];
    
    $bulan_stats[] = [
        'bulan' => $bulan_label,
        'total' => $total,
        'disetujui' => $disetujui
    ];
}

// Statistik per aula
$aula_stats = mysqli_query($conn,
    "SELECT a.nama_aula, COUNT(h.id) as total
     FROM aula a
     LEFT JOIN histori_penggunaan h ON a.id = h.aula_id
     GROUP BY a.id
     ORDER BY total DESC"
);

// Statistik per seksi
$seksi_stats = mysqli_query($conn,
    "SELECT s.nama_seksi, COUNT(p.id) as total
     FROM seksi s
     LEFT JOIN pengajuan_aula p ON s.id = p.seksi_id AND p.status_id = 2
     GROUP BY s.id
     ORDER BY total DESC"
);

// Total keseluruhan
$total_pengajuan = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM pengajuan_aula"))['total'];
$total_disetujui = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 2"))['total'];
$total_ditolak = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 3"))['total'];
$total_dibatalkan = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pengajuan_aula WHERE status_id = 4"))['total'];

include '../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card stat-primary">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3><?php echo $total_pengajuan; ?></h3>
            <p>Total Pengajuan</p>
        </div>
    </div>
    
    <div class="stat-card stat-success">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <h3><?php echo $total_disetujui; ?></h3>
            <p>Disetujui</p>
        </div>
    </div>
    
    <div class="stat-card stat-danger">
        <div class="stat-icon">❌</div>
        <div class="stat-info">
            <h3><?php echo $total_ditolak; ?></h3>
            <p>Ditolak</p>
        </div>
    </div>
    
    <div class="stat-card stat-secondary">
        <div class="stat-icon">🚫</div>
        <div class="stat-info">
            <h3><?php echo $total_dibatalkan; ?></h3>
            <p>Dibatalkan</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Tren Pengajuan (6 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Total Pengajuan</th>
                            <th>Disetujui</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bulan_stats as $stat): ?>
                        <tr>
                            <td><strong><?php echo $stat['bulan']; ?></strong></td>
                            <td><?php echo $stat['total']; ?></td>
                            <td><?php echo $stat['disetujui']; ?></td>
                            <td>
                                <?php 
                                $persen = $stat['total'] > 0 ? round(($stat['disetujui'] / $stat['total']) * 100, 1) : 0;
                                echo $persen . '%';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Statistik per Aula</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Aula</th>
                            <th class="text-right">Total Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($a = mysqli_fetch_assoc($aula_stats)): ?>
                        <tr>
                            <td><?php echo $a['nama_aula']; ?></td>
                            <td class="text-right"><strong><?php echo $a['total']; ?>x</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Statistik per Seksi</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Seksi</th>
                            <th class="text-right">Total Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($s = mysqli_fetch_assoc($seksi_stats)): ?>
                        <tr>
                            <td><?php echo $s['nama_seksi']; ?></td>
                            <td class="text-right"><strong><?php echo $s['total']; ?>x</strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>