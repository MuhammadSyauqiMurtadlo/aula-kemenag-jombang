<?php
session_start();
require_once '../config/database.php';

check_role([2]);
$page_title = 'Laporan';

// Filter
$bulan = isset($_GET['bulan']) ? clean_input($_GET['bulan']) : date('Y-m');

$where = "p.status_id = 2";
if ($bulan) {
    $where .= " AND DATE_FORMAT(p.tanggal_mulai, '%Y-%m') = '$bulan'";
}

$query = "SELECT p.*, a.nama_aula, s.nama_seksi, u.nama_lengkap as pemohon
          FROM pengajuan_aula p
          LEFT JOIN aula a ON p.aula_id = a.id
          LEFT JOIN seksi s ON p.seksi_id = s.id
          LEFT JOIN users u ON p.user_id = u.id
          WHERE $where
          ORDER BY p.tanggal_mulai DESC, p.waktu_mulai";

$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Laporan Penggunaan Aula</h3>
    </div>

    <div class="card-body">
        <form method="GET" class="filter-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Bulan</label>
                        <input type="month" name="bulan" class="form-control" value="<?php echo $bulan; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary">🔍 Filter</button>
                        <a href="laporan.php" class="btn btn-secondary">Reset</a>
                        <button type="button" onclick="window.print()" class="btn btn-success">🖨️ Cetak</button>
                    </div>
                </div>
            </div>
        </form>

        <hr>

        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">Tidak ada data untuk ditampilkan</div>
        <?php else: ?>
        
        <h5>Total: <?php echo mysqli_num_rows($result); ?> penggunaan</h5>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Kegiatan</th>
                        <th>Aula</th>
                        <th>Seksi</th>
                        <th>Pemohon</th>
                        <th>Peserta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo format_tanggal($row['tanggal_mulai']); ?></td>
                        <td><?php echo substr($row['waktu_mulai'], 0, 5); ?> - <?php echo substr($row['waktu_selesai'], 0, 5); ?></td>
                        <td><?php echo $row['nama_kegiatan']; ?></td>
                        <td><?php echo $row['nama_aula']; ?></td>
                        <td><?php echo $row['nama_seksi']; ?></td>
                        <td><?php echo $row['pemohon']; ?></td>
                        <td><?php echo $row['jumlah_peserta'] ?? '-'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>